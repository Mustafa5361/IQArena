<?php

require_once "dbConnection.php";

$db = new dbConnection();

function PastMatch($playerID)
{

    global $db;

    $query = $db -> fetchAll(
           "SELECT p1.username AS thisUsername,
            p2.username AS enemyUsername,
            rp1.point AS thisPoint,
            rp2.point AS enemyPoint,
            rp1.cupQuantity AS thisCup,
            rp1.winner AS thisStatus,
            er.roomID,
            SUM(a1.answertime) AS thisAnswerTime,
            SUM(a2.answertime) AS enemyAnswerTime

            FROM encounterroom er
            JOIN roomplayer rp1 ON er.roomID = rp1.roomID
            JOIN roomplayer rp2 ON er.roomID = rp2.roomID AND rp1.roomplayerID != rp2.roomplayerID
            JOIN player p1 ON rp1.playerID = p1.playerID
            JOIN player p2 ON rp2.playerID = p2.playerID

            -- Cevap süreleri için answers tablosuna LEFT JOIN
            LEFT JOIN answers a1 ON a1.roomplayerID = rp1.roomplayerID
            LEFT JOIN answers a2 ON a2.roomplayerID = rp2.roomplayerID

            WHERE p1.playerID = :playerID
            AND er.STATUS = 'finished'

            GROUP BY er.roomID, p1.username, p2.username, rp1.point, rp2.point, rp1.cupQuantity, rp1.winner

            ORDER BY er.roomID DESC
            LIMIT 20",

            ["playerID" => $playerID]
        );
    
    return $query;

}

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    $value = json_decode($_POST["value"]);

    if(isset($value -> rank))
    {

        $query = $db -> fetchAll(
            "SELECT username, cup, point
            FROM player
            WHERE isDeleted = 0
            ORDER BY cup DESC, point DESC
            LIMIT 100;",
            []
        );

        echo json_encode(["ranks" => $query]);

    }
    else // value => token
    {

        $query = $db -> fetch(
            "SELECT playerID FROM tokens where token = :token",
            ["token" => $value -> token]
        );

        $playerData = $db -> fetch("SELECT username , point , cup from player where playerID = :playerID",
         [
            "playerID" => $query["playerID"]
         ]);
        echo json_encode(["userName" => $playerData["username"], "cup" => $playerData["cup"] , "point" => $playerData["point"] , "matchHistories" => PastMatch($query["playerID"])]);

    }

}

?>