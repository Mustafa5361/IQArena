
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    require_once "dbConnection.php";
    require_once "cupCalculation.php";

    $value = json_decode($_POST["value"]);

    $db = dbConnection();

    if(isset($value -> token))
    {

        $playerID = $db -> fetch(
            "SELECT playerID from tokens where token = :token ",
            [
                "token" => $value -> token
            ]
        )["playerID"];

        $enemyData = $db -> fetch(
            "SELECT playerID, point from roomplayer
            where playerID != :playerID
            and roomID = :roomID",
            [
                "playerID" => $playerID,
                "roomID" => $value -> roomID
            ]
        );
                
        if(isset($enemyData["point"]))
        {

            $players = $db->fetchAll(
                "SELECT * FROM roomplayer rp INNER JOIN player p ON p.playerID = rp.playerID WHERE roomID = :roomID",
                [
                    "roomID" => $value -> roomID
                ]
            );

            [$winner, $loser] = ($players[0]["point"] > $players[1]["point"]) ? [$players[0], $players[1]] : [$players[1], $players[0]];
            
            $calculation = new cupCalculation();
            $calculate = $calculation->calculate($winner["point"], $loser["point"]);

            $playerIsWinner = $winner["playerID"] == $playerID;

            echo json_encode(["Question" => null, "Finish" => [
                        "finished" => true,
                        "thisUsername" => $playerIsWinner ? $winner["username"] : $loser["username"],
                        "enemyUsername" => $playerIsWinner ? $loser["username"] : $winner["username"],
                        "thisPoint" => $playerIsWinner ? $winner["point"] : $loser["point"],
                        "enemyPoint" => $playerIsWinner ? $loser["point"] : $winner["point"],
                        "thisCupChange" => $playerIsWinner ? $calculate["cupGain"] : -$calculate["cupLose"],
                        "thisStatus" => $playerIsWinner ? "Win" : "Lose",
                    ]]);
            
        }
        else{
            echo json_encode(["finished" => false, "message" => "Rakip Oyunu Bitirmedi."]);
        }

    }

}

?>
