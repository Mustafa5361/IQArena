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
        
        //token uzerinden playerID ye ulaşılıyor.
        $playerID = $db->fetch( 
            "SELECT player.playerID
             FROM player
             INNER JOIN tokens ON tokens.playerID = player.playerID
             WHERE tokens.token = :token",
            ["token" => $value -> token]
        )["playerID"];
        
        //gelen playerın playerID1 mi yoksa PlayerID2 de mi saklandıgını ögreniyoruz.
        $player1OR2 = $db -> fetch(
            "SELECT roomID
            FROM encounterroom
            where playerID1 = :playerID AND status = 'matched'",
            ["playerID" => $playerID]
        );

        if($player1OR2 != false) // gelen veriye göre playerID1 de saklanan player bulunuyor.
            global $player1OR2 = "1";
        else
            global $player1OR2 = "2";

        $delay = $db -> fetch(
            "SELECT delay{$player1OR2} from encounterroom 
            where playerID{$player1OR2} = :playerID and status = 'matched'",
            [
                "playerID" => $playerID
            ]
        )["delay"];

        //ne kadar sürede cevap verdiği ve cevabı tutuluyor.
        $db->query(
            "UPDATE matchquestions
            SET player{$player1OR2}Answer = :answer, time = :time
            WHERE roomID = :roomID AND questionID = :questionID",
            [
                "answer" => $value->answer,
                "time" => time(),
                "roomID" => $roomID->roomID,
                "questionID" => $value->questionID
            ]
        );

        //bulunan oyuncunun en son gelen haber süresini tutuyor.
        $db->query(
            "UPDATE encounterroom
            SET delay{$player1OR2} = :delay
            WHERE playerID{$player1OR2} = :playerID AND status = 'matched'",
            [
                "delay" => time(),
                "playerID" => $playerID
            ]
        );

        //bir sonraki soruyu seçmek için kullanılan sorgu
        $questionIndex = $db -> fetch(
            "SELECT question.question, question.answerA, question.answerB, question.answerC, question.answerD
            From question inner Join metchquestion ON question.questionID = metchquestion.questionID
            where metchquestion.roomID = :roomID 
            and metchquestion.questionIndex = (Select questionIndex from metchquestion 
                                                where roomID = :roomID and questionID = :questionID limit 1) + 1",
            [
                "roomID" => $value -> roomID, 
                "questionID" => $value -> questionID
            ]
        );

        if($questionIndex == false)
        {

            //cevaplar ve kazanılan puan hesaplanıcak

        }
        else
            echo json_encode($questionIndex);

    } 
    else 
    { 
        // Eşleşme sistemi
        // Eşleşme sisteminde uzun süreli haber kesileni eşleşmeden çıkarma
        $db -> query(
            "DELETE from `match` 
            where time > :time"
            [
                "time" => (time() - 4);
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

        if (!$query) 
        {
            exit(json_encode(["success" => false, "message" => "Invalid token"]));
        }

        // 2. Oyuncu daha önce eşleşmiş mi?
        $roomControl = $db->fetch(
            "SELECT roomID
             FROM encounterroom
             WHERE playerID2 = :playerID AND status = 'matched'",
            ["playerID" => $query["playerID"]]
        );

        if ($roomControl != false) 
        {
            exit(json_encode(["success" => true, "roomID" => $roomControl["roomID"]]));
        }

        // 3. Oyuncu daha önce eşleşme aramış mı?
        $queryControl = $db -> fetch(
            "SELECT startTime
             FROM `match`
             WHERE playerID = :playerID",
            ["playerID" => $query["playerID"]]
        );

        $avarage = 15; // Başlangıçta eşleşme kupa aralığı

        if ($queryControl != false) 
        {
            $avarage = (time() - $queryControl["startTime"]) + $avarage;
        }

        $minCup = $query["cup"] - $avarage;
        $maxCup = $query["cup"] + $avarage;

        // 4. Rakip arıyoruz
        $queryMatch = $db -> fetch(
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

        if ($queryMatch != false) 
        {
            // Rakip bulunduysa encounterroom'a kayıt
            $db->insert("encounterroom", [
                "playerID1" => $query["playerID"],
                "playerID2" => $queryMatch["playerID"],
                "status" => 'matched'
            ]);

            $roomControl = $db -> fetch(
                "select roomID from encounterroom 
                where status = 'matched' and playerID1 = :playerID1 and playerID2 = :playerID2 "
                [
                    "playerID1" => $query["playerID"],
                    "playerID2" => $queryMatch["playerID"]
                ]  
            );
            
            $createQuestion = new createQuestion();

            $questions = $createQuestion -> selectRandomQuestion();

            foreach($questions as $key => $question)
            {

                $db -> insert("matchquestions",
                [
                    "roomID" => $roomControl["roomID"],
                    "questionID" => $question,
                    "questionIndex" => $key 
                ]
                );

            }

            // Eşleşen iki oyuncuyu `match` tablosundan siliyoruz
            $db -> query(
                "DELETE FROM `match`
                 WHERE playerID = :playerID1 OR playerID = :playerID2",
                [
                    "playerID1" => $query["playerID"],
                    "playerID2" => $queryMatch["playerID"]
                ]
            );

            exit(json_encode(["success" => true, "roomID" => $roomControl["roomID"]]));
        } 
        else 
        {
            // Rakip bulunamadıysa `match` tablosuna kayıt veya güncelleme

            if ($queryControl != false) 
            {
                // Daha önceden arıyorsa sadece time güncellenir
                $db -> query(
                    "UPDATE `match`
                     SET time = :time
                     WHERE playerID = :playerID",
                    [
                        "time" => time(),
                        "playerID" => $query["playerID"]
                    ]
                );
            } 
            else 
            {
                // İlk kez arıyorsa `match` tablosuna eklenir
                $db -> insert("`match`", [
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
