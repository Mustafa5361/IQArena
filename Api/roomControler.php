<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    require_once "dbConnection.php";
    require_once "createQuestion.php";
    require_once "cupCalculation.php";

    $value = json_decode($_POST["value"]);
    $db = new dbConnection();

    if (isset($value->roomID)) {

        if ($value->answer != "None") {
            $query = $db->fetch(
                "SELECT rp.roomplayerID, rp.delay, rp.roomID, rp.playerID FROM roomplayer rp 
                INNER JOIN tokens t ON t.playerID = rp.playerID
                INNER JOIN encounterroom ect ON rp.roomID = ect.roomID
                WHERE ect.status = 'matched' AND t.token = :token",
                ["token" => $value->token]
            );

            if (!$query) exit(json_encode(["error" => "Player not matched or invalid token"]));

            $roomPlayerID = $query["roomplayerID"];
            $delay = $query["delay"];
            $roomID = $query["roomID"];
            $playerID = $query["playerID"];

            $enemy = $db->fetch(
                "SELECT roomplayerID, delay, playerID FROM roomplayer 
                WHERE roomID = :roomID AND roomplayerID != :roomplayerID",
                ["roomID" => $roomID, "roomplayerID" => $roomPlayerID]
            );

            if ($enemy && ($enemy["delay"] + 32 < time())) {
                $players = $db->fetchAll(
                    "SELECT * FROM roomplayer rp 
                    INNER JOIN player p ON rp.playerID = p.playerID
                    WHERE rp.roomID = :roomID",
                    ["roomID" => $roomID]
                );
                
                //OYUN SONU OLARAK DÜZENLENECEK.

                $winner = ($players[0]["playerID"] == $enemy["playerID"]) ? $players[1] : $players[0];
                $loser = ($players[0]["playerID"] == $enemy["playerID"]) ? $players[0] : $players[1];

                $calculation = new cupCalculation();
                $calculate = $calculation->calculate($winner["cup"], $loser["cup"]);

                WinnerLoser($db, $winner, $loser, $calculate["cupGain"], $calculate["cupLose"], $roomID);
            }

            $matchQuestionID = $db->fetch(
                "SELECT matchquestionID FROM matchquestions
                WHERE roomID = :roomID AND questionID = :questionID",
                ["roomID" => $roomID, "questionID" => $value->questionID]
            )["matchquestionID"] ?? null;

            if (!$matchQuestionID) exit(json_encode(["error" => "No such question in room"]));

            $time = time() - $delay;

            $db->insert("answers", [
                "roomplayerID" => $roomPlayerID,
                "matchquestionID" => $matchQuestionID,
                "answer" => $value->answer,
                "answertime" => $time
            ]);

            $db->query("UPDATE roomplayer SET delay = :delay WHERE roomplayerID = :roomplayerID", [
                "delay" => time(),
                "roomplayerID" => $roomPlayerID
            ]);

            $question = $db->fetch(
                "SELECT q.questionID, q.question, q.answerA, q.answerB, q.answerC, q.answerD 
                FROM question q 
                INNER JOIN matchquestions m ON q.questionID = m.questionID 
                WHERE m.roomID = :roomID AND m.questionIndex = (
                    SELECT questionIndex FROM matchquestions WHERE matchquestionID = :matchQuestionID
                ) + 1",
                ["roomID" => $roomID, "matchQuestionID" => $matchQuestionID]
            );

            if ($question) {
                echo json_encode(["Question" => $question]);
            } else {
                $answers = $db->fetchAll("SELECT matchquestionID, answer, answertime FROM answers WHERE roomplayerID = :roomplayerID", ["roomplayerID" => $roomPlayerID]);

                $totalScore = 0;
                foreach ($answers as $answer) {
                    $correct = $db->fetch(
                        "SELECT q.difficultyLevel, q.correctAnswer FROM matchquestions m 
                        INNER JOIN question q ON q.questionID = m.questionID 
                        WHERE m.matchquestionID = :matchquestionID",
                        ["matchquestionID" => $answer["matchquestionID"]]
                    );
                    if ($correct["correctAnswer"] == $answer["answer"]) {
                        $totalScore += ($answer["answertime"] * 1.5 + $correct["difficultyLevel"] * 1.7 + 20);
                    }
                }

                $db->query("UPDATE roomplayer SET point = :point WHERE roomplayerID = :roomplayerID", [
                    "point" => $totalScore,
                    "roomplayerID" => $roomPlayerID
                ]);

                echo json_encode(["Question" => null , "Finish" => ["finished" => false, "thisPoint" => $totalScore]]);

            }
        } else {
            $question = $db->fetch("SELECT q.questionID, q.question, q.answerA, q.answerB, q.answerC, q.answerD FROM question q INNER JOIN matchquestions m ON q.questionID = m.questionID WHERE m.roomID = :roomID AND m.questionIndex = 0", ["roomID" => $value->roomID]);
            echo json_encode(["Question" => $question, "Finish" => null]);
        }
    } else {
        // Eşleşme sistemi
        // Eşleşme sisteminde uzun süreli haber kesileni eşleşmeden çıkarma
        $db -> query(
            "DELETE from `match`
            where `time` < :time",
            [
                "time" => (time() - 4)
            ]
        );

        // 1. Oyuncunun bilgilerini getir (token kontrolü)
        $query = $db->fetch(
            "SELECT player.playerID, player.cup
            FROM player
            INNER JOIN tokens ON tokens.playerID = player.playerID WHERE tokens.token = :token",
            ["token" => $value->token]
        );

        if (!$query) {
            exit(json_encode(["success" => false, "message" => "Invalid token"]));
        }

        // 2. Oyuncu daha önce eşleşmiş mi?
        $roomControl = $db->fetch(
            "SELECT ect.roomID FROM encounterroom ect inner join roomplayer rp on ect.roomID = rp.roomID
            where rp.playerID = :playerID and ect.status = 'matched'",
            ["playerID" => $query["playerID"]]
        );

        if ($roomControl != false) {
            exit(json_encode(["success" => true, "roomID" => $roomControl["roomID"]]));
        }

        // 3. Oyuncu daha önce eşleşme aramış mı?
        $queryControl = $db->fetch(
            "SELECT startTime
            FROM `match`
            WHERE playerID = :playerID",
            ["playerID" => $query["playerID"]]
        );

        $avarage = 15;
        if ($queryControl != false) {
            $avarage = (time() - $queryControl["startTime"]) + $avarage;
        }

        $minCup = $query["cup"] - $avarage;
        $maxCup = $query["cup"] + $avarage;

        // 4. Rakip arıyoruz
        $queryMatch = $db->fetch(
            "SELECT playerID
            FROM `match`
            WHERE playerID != :playerID
            AND cup BETWEEN :minCup AND :maxCup
            LIMIT 1",
            [
                "playerID" => $query["playerID"],
                "minCup" => $minCup,
                "maxCup" => $maxCup
            ]
        );

        if ($queryMatch != false) {

            //encounterrooma oda oluşturma
            $roomID = $db->insert("encounterroom", 
            [
                "status" => 'matched'
            ]);

            // roomplayer tablosu insert işlemi ID döndürüyorsa doğrudan kullan
            //1.oyuncu
            $db -> insert("roomplayer",
            [
                "roomID" => $roomID,
                "playerID" => $query["playerID"],
                "delay" => time()
            ]);
            //2.oyuncu
            $db -> insert("roomplayer",
            [
                "roomID" => $roomID,
                "playerID" => $queryMatch["playerID"],
                "delay" => time()
            ]);
            
            // Eşleşen iki oyuncuyu `match` tablosundan siliyoruz
            $db->query(
                "DELETE FROM `match`
                WHERE playerID = :playerID1 OR playerID = :playerID2",
                [
                    "playerID1" => $query["playerID"],
                    "playerID2" => $queryMatch["playerID"]
                ]
            );

            $createQuestion = new createQuestion();
            $questions = $createQuestion->selectRandomQuestion();

            foreach ($questions as $key => $question) {
                $db->insert("matchquestions", [
                    "roomID" => $roomID,
                    "questionID" => $question["questionID"],
                    "questionIndex" => $key
                ]);
            }

            echo(json_encode(["success" => true, "roomID" => $roomID]));
            
        } else {

            // Rakip bulunamadıysa `match` tablosuna kayıt veya güncelleme
            if ($queryControl != false) 
            {
                $db->query(
                    "UPDATE `match`
                    SET `time` = :time
                    WHERE playerID = :playerID",
                    [
                        "time" => time(),
                        "playerID" => $query["playerID"]
                    ]
                );
            } else 
            {
                $db->insert("`match`", [
                    "playerID" => $query["playerID"],
                    "startTime" => time(),
                    "cup" => $query["cup"],
                    "time" => time()
                ]);
            }

            echo(json_encode(["success" => false, "roomID" => ""]));

        }

    }
}

function WinnerLoser($db, $winner, $loser, $cupGain, $cupLose, $roomID) {
    $db->query("UPDATE roomplayer SET winner = 'Win', cupQuantity = cupQuantity + :cup WHERE roomplayerID = :id", ["cup" => $cupGain, "id" => $winner["roomplayerID"]]);
    $db->query("UPDATE roomplayer SET winner = 'Lose', cupQuantity = cupQuantity - :cup WHERE roomplayerID = :id", ["cup" => $cupLose, "id" => $loser["roomplayerID"]]);
    $db->query("UPDATE player SET cup = cup + :cup WHERE playerID = :id", ["cup" => $cupGain, "id" => $winner["playerID"]]);
    $db->query("UPDATE player SET cup = cup - :cup WHERE playerID = :id", ["cup" => $cupLose, "id" => $loser["playerID"]]);
    $db->query("UPDATE encounterroom SET status = 'finished' WHERE roomID = :roomID", ["roomID" => $roomID]);
}

?>
