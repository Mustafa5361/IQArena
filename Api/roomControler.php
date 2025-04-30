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

    if (isset($value->answer))
    {
        
        //token uzerinden eşleşmedeki playerRoomID ye erişiyoruz.
        $roomPlayerID = $db -> fetch( 
            "SELECT rm.roomplayerID
            FROM roomplayer rm
            JOIN encounterroom er 
                ON (rm.roomplayerID = er.roomplayerID1 OR rm.roomplayerID = er.roomplayerID2)
                WHERE rm.playerID = (
                SELECT p.playerID
                FROM player p
                JOIN tokens t ON t.playerID = p.playerID
                WHERE t.token = :token
            )
            AND er.status = 'matched'",
            ["token" => $value -> token]
        )["roomplayerID"];
        
        
        $delay = $db -> fetch(
            "SELECT delay from roomplayer
            where roomplayerID =  :roomPlayerID",
            [
                ":roomPlayerID" => $roomPlayerID
            ]
        )["delay"];
        
        //matchquestions tablosundan sorunun bulundugu indexi çekiyoruz.
        $matchQuestionID = $db -> fetch(
            "SELECT matchquestionID from matchquestions
            where roomID = :roomID and questionID = :questionID",
            [
                "roomID" => $value -> roomID,
                "questionID" => $value -> questionID
            ]
        )["matchQuestionID"];

        //ne kadar sürede cevap verdiği ve cevabı tutuluyor.
        $db -> insert("answer",
        [
            "roomplayerID" => $roomPlayerID,
            "matchquestionID" => $matchQuestionID,
            "answer" => $value -> answer
        ]);

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
            "SELECT e.roomID
            FROM encounterroom e
            INNER JOIN roomplayer rp ON rp.roomplayerID = e.roomplayerID1
            WHERE rp.playerID = :playerID AND e.status = 'matched'
            UNION
            SELECT e.roomID
            FROM encounterroom e
            INNER JOIN roomplayer rp ON rp.roomplayerID = e.roomplayerID2
            WHERE rp.playerID = :playerID AND e.status = 'matched'",
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
            // Rakip bulunduysa encounterroom'a kayıt
            $roomplayerID1 = $db->insert("roomplayer", [
                "playerID" => $query["playerID"],
                "delay" => time()
            ]);

            $roomplayerID2 = $db->insert("roomplayer", [
                "playerID" => $queryMatch["playerID"],
                "delay" => time()
            ]);

            // roomplayer tablosu insert işlemi ID döndürüyorsa doğrudan kullan
            $roomID = $db->insert("encounterroom", [
                "roomplayerID1" => $roomplayerID1,
                "roomplayerID2" => $roomplayerID2,
                "status" => 'matched'
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
?>
