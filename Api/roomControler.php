<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    require_once "dbConnection.php";
    require_once "createQuestion.php";

    $value = json_decode($_POST["value"]);

    $db = new dbConnection();

    /*

        $value de gelen veriler
        token
        questionID
        answer
        roomID

    */
    if(isset($value -> roomID))
    {
        if ($value -> answer != "none")
        {
            
            //token uzerinden eşleşmedeki playerRoomID ye erişiyoruz.
            $query = $db -> fetch( 
                "SELECT  rp.roomplayerID, rp.delay FROM roomplayer rp inner  Join  tokens t ON t.playerID = rp.playerID
                inner Join encounterroom ect on rp.roomID = ect.roomID
                where ect.status = 'matched' and t.token = :token ",
                ["token" => $value -> token]
            )["roomplayerID"];
            
            $roomPlayerID = $query["playerID"];
            $delay = $query["delay"];
            // rakibin gecikmesinni hesaplama
            $enemy = $db -> fetch(
                "SELECT roomplayerID, delay from roomplayer 
                where roomID = :roomID and roomplayerID != :roomplayerID ",
                [
                    "roomID" => $query -> roomID,
                    "roomplayerID" => $roomPlayerID
                ]
            );

            if(($enemy["delay"] + 6) < time())
            {
                
                $players = $db -> fetchAll(
                    "SELECT * From roomplayer rp inner join player p
                    ON rp.playerID = p.playerID
                    where rp.roomID = :roomID",
                    [
                        "roomID" => $value -> roomID
                    ]
                );

                if($players[0]["playerID"] == $enemy["playerID"])
                {
                    $winner = $players[1];
                    $loser = $players[0];
                }
                else
                {
                    $winner = $players[0];
                    $loser = $players[1];
                }

                WinnerLoser($db, $winner, $loser, $calculate["cupGain"], $calculate["cupLose"]);

            }

            //matchquestions tablosundan sorunun bulundugu indexi çekiyoruz.
            $matchQuestionID = $db -> fetch(
                "SELECT matchquestionID from matchquestions
                where roomID = :roomID and questionID = :questionID",
                [
                    "roomID" => $value -> roomID,
                    "questionID" => $value -> questionID
                ]
            )["matchQuestionID"];
            
            $time = time() - $delay;
    
            //ne kadar sürede cevap verdiği ve cevabı tutuluyor.
            $db -> insert("answer",
            [
                "roomplayerID" => $roomPlayerID,
                "matchquestionID" => $matchQuestionID,
                "answer" => $value -> answer,
                "answertime" => $time
            ]);
    
            $db -> query(
                "UPDATE roomplayer set delay = :delay
                where roomplayerID = :roomplayerID",
                [
                    "delay" => time(),
                    "roomplayerID" => $roomPlayerID
                ]
            );
    
            $question = $db ->  fetch(
                "SELECT q.questionID, q.question, q.answerA, q.answerB, q.answerC, q.answerD 
                FROM question q INNER JOIN matchquestion m 
                ON q.questionID=m.questionID 
                where m.roomID =  :roomID 
                AND m.questionIndex = ( SELECT questionIndex FROM matchquestion
                where matchquestionID = :matchquestionID ) + 1",
                [
                    "matchquestionID" => $matchQuestionID,
                    "roomID" => $value -> roomID
                ]
            );
    
            if ($question != false)
            {
                echo json_encode($question);
            }
            else 
            {
    
                $answers = $db -> fetchAll(
                    "SELECT matchquestionID, answer, answertime from answer 
                    where roomplayerID = :roomplayerID ",
                    [
                        "roomplayerID" => $roomPlayerID
                    ]
                );
    
                $totalScore = 0;
    
                foreach($answers as $answer)
                {
    
                    $correctAnswer =  $db -> fetch(
                        "SELECT q.difficultyLevel q.answer from matchquestion m INNER JOIN question q 
                        ON q.questionID = m.questionID 
                        where  m.matchquestionID = :matchquestionID",
                        [
                            "matchquestionID" => $answer["matchquestionID"]
                        ]
                    );
    
                    //puan hesaplama düzenlennebilir.
    
                    if($correctAnswer["answer"] == $answer["answer"])
                    {
                        $totalScore += ($answer["answertime"] * 1.5 + $correctAnswer["difficultyLevel"] * 1.7 + 20);
                    }
    
                }
    
                $db -> query(
                    "UPDATE roomplayer 
                    set point = :point
                    where roomplayerID = :roomplayerID",
                    [
                        "roomplayerID" => $roomPlayerID,
                        "point" => $totalScore
                    ]
                );
    
                $pointControl = $db -> fetch(
                    "SELECT rp.point, rp.playerID FROM roomplayer rp inner join encounterroom ect
                    ON rp.roomID = ect.roomID
                    where roomPlayerID != :roomplayerID and roomID = :roomID
                    ",
                    [
                        "roomplayerID" => $roomPlayerID,
                        "roomID" => $value -> roomID
                    ]
                );
    
                if($pointControl["point"] != null)
                {
                    // 2 oyuncunun bilgilerini al
                    $players = $db->fetchAll(
                        "SELECT * FROM roomplayer rp inner join player p
                        ON p.playerID = rp.playerID
                         WHERE roomID = :roomID",
                        ["roomID" => $value->roomID]
                    );
    
                    $player1 = $players[0];
                    $player2 = $players[1];
    
                    // Kazanan ve kaybedeni belirle
                    if ($player1["point"] > $player2["point"]) {
                        $winner = $player1;
                        $loser = $player2;
                    } else {
                        $winner = $player2;
                        $loser = $player1;
                    }
    
                    // Kupa hesaplama
                    $cupCalculation = new cupCalculation();    
                    $calculate = $cupCalculation->calculate($winner["cup"], $loser["cup"]);

                    WinnerLoser($db, $winner, $loser, $calculate["cupGain"], $calculate["cupLose"]);

                }
    
    
            }
    
        } 
        else // ilk soru
        {
            $question = $db -> fetch(
                "SELECT q.questionID, q.question, q.answerA, q.answerB, q.answerC, q.answerD 
                FROM question q INNER JOIN matchquestion m 
                ON q.questionID=m.questionID 
                where m.roomID =  :roomID 
                AND m.questionIndex = 0",
                [
                    "roomID" => $value -> roomID
                ]
            );
            echo json_encode($question);
        }
    }
    else 
    { 
        // Eşleşme sistemi
        // Eşleşme sisteminde uzun süreli haber kesileni eşleşmeden çıkarma
        $db -> query(
            "DELETE from match 
            where time > :time",
            [
                "time" => (time() - 4)
            ]
        );

        // 1. Oyuncunun bilgilerini getir (token kontrolü)
        $query = $db->fetch(
            "SELECT player.playerID, player.cup
            FROM player
            INNER JOIN tokens ON tokens.playerID = player.playerID
            WHERE tokens.token = :token",
            ["token" => $value->token]
        );

        if (!$query) {
            exit(json_encode(["success" => false, "message" => "Invalid token"]));
        }

        // 2. Oyuncu daha önce eşleşmiş mi?
        $roomControl = $db->fetch(
            "SELECT ect.roomID FROM encounterroom ect inner join roomplayer rp on ect.roomID = rp.roomID
            where rp.roomplayerID =  :playerID and ect.status = 'matched'",
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

            //eencounterrooma oda oluşturma
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
            
            $createQuestion = new createQuestion();
            $questions = $createQuestion->selectRandomQuestion();

            foreach ($questions as $key => $question) {
                $db->insert("matchquestions", [
                    "roomID" => $roomID,
                    "questionID" => $question,
                    "questionIndex" => $key
                ]);
            }

            // Eşleşen iki oyuncuyu `match` tablosundan siliyoruz
            $db->query(
                "DELETE FROM `match`
                WHERE playerID = :playerID1 OR playerID = :playerID2",
                [
                    "playerID1" => $query["playerID"],
                    "playerID2" => $queryMatch["playerID"]
                ]
            );

            exit(json_encode(["success" => true, "roomID" => $roomID]));
        } else {
            // Rakip bulunamadıysa `match` tablosuna kayıt veya güncelleme
            if ($queryControl != false) {
                $db->query(
                    "UPDATE `match`
                    SET time = :time
                    WHERE playerID = :playerID",
                    [
                        "time" => time(),
                        "playerID" => $query["playerID"]
                    ]
                );
            } else {
                $db->insert("`match`", [
                    "playerID" => $query["playerID"],
                    "startTime" => time(),
                    "cup" => $query["cup"],
                    "time" => time()
                ]);
            }

            exit(json_encode(["success" => false, "roomID" => ""]));
        }

    }

}

function WinnerLoser($db, $winner, $loser, $cupGain, $cupLose)
{
    // Winner/loser bilgilerini veritabanına yaz
    $db->query(
        "UPDATE roomplayer SET winner = 'Win' 
        WHERE roomplayerID = :id", 
        [
            "id" => $winner["roomplayerID"]
        ]);
    $db->query(
        "UPDATE roomplayer SET winner = 'Lose' 
        WHERE roomplayerID = :id", 
        [
            "id" => $loser["roomplayerID"]
        ]);

    // roomplayer tablosundaki cupQuantity güncelle
    $db->query(
        "UPDATE roomplayer SET cupQuantity = cupQuantity + :cup 
        WHERE roomplayerID = :id", 
    [
        "cup" => $cupGain,
        "id" => $winner["roomplayerID"]
    ]);
    $db->query(
        "UPDATE roomplayer SET cupQuantity = cupQuantity - :cup 
        WHERE roomplayerID = :id", 
    [
        "cup" => $cupLose,
        "id" => $loser["roomplayerID"]
    ]);

    // player tablosundaki toplam kupaları da güncelle (isteğe bağlı)
    $db->query(
        "UPDATE player SET cup = cup + :cup 
        WHERE playerID = :id", 
    [
        "cup" => $cupGain,
        "id" => $winner["playerID"]
    ]);
    $db->query(
        "UPDATE player SET cup = cup - :cup
         WHERE playerID = :id", 
    [
        "cup" => $cupLose,
        "id" => $loser["playerID"]
    ]);
}

?>
