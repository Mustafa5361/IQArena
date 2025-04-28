<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    require_once "dbConnection.php";

    $value = json_decode($_POST["value"]);

    $db = new dbConnection();

    if (isset($value->answer)) 
    {
        // Eğer 'answer' varsa burada cevap işleme yapılacak (Henüz boş bırakılmış)
    } 
    else 
    { 
        // Eşleşme sistemi
        //eşleşme sisteminde uzun süreli haber kesileni eşleşmeden çıkarma
        $db -> query(
            "delete from `match` 
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
        $queryControl = $db->fetch(
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

            // Eşleşen iki oyuncuyu `match` tablosundan siliyoruz
            $db->query(
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
                $db->query(
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
