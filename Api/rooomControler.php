<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    require_once "dbConnection.php";

    $value = json_decode($_POST["value"]));

    $db = new dbConnection();

    if(isset($value -> answer))
    {

    }
    else // eşleşme sistemi
    {

        $query = $db -> fetch(
            "select playerID, cup from player,token 
            where (token.playerID = player.playerID) and token.token = :token ",
            ["token" => $value -> token]
        );
        
        $queryControl = $db -> fetch( //ne kadar süredir eşleşme aradıgını kontrol ediyoruz.
            "select startTime from match 
            where playerID = :playerID",
            ["playerID" => $query["playerID"]]
        );

        $avarage = 15; //eşleşmede kupa aralıgı

        if($queryControl != false)
            $avarage = (time() - $queryControl["startTime"]) + $avarage;

        
        $minCup = $query["cup"] - $avarage;
        $maxCup = $query["cup"] + $avarage;

        $queryMatch = $db -> fetch(
            "select playerID from match 
            where playerID =! :playerID and cup between :minCup and :maxCup"
            ["playerID" => $query["playerID"], "minCup" => $minCup, "maxCup" => $maxCup]
        );
        
        if($queryMatch != false)
        {

            $db -> insert("encounterroom",["playerID1" => $query["playerID"], "playerID2" => $queryMatch["playerID"]]);

        }
        else
        {

            if($queryControl != false)
            {
                
                $db -> query(
                    "update match set time = :time where playerID = :playerID",
                    ["time" => time(), "playerID" => $query["playerID"]]
                );

            }
            else
            {
                $db -> insert("match",["playerID" => $query["playerID"], "startTime" => time()]);
            }


        }

    }


}

?>