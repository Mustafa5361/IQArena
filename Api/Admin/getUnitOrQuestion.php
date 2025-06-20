<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    require_once "dbConnection.php";

    $value = json_decode($_POST["value"]);

    $db = new dbConnection();

    if(isset($value -> token))
    {

        $query = $db -> fetch("SELECT playerID from tokens where token = :token", ["token" => $value -> token]);

        if($query["playerID"] == 1)
        {

            if($value -> desired == "unit")
            {

                $query = $db -> fetch("SELECT * from unit", []);

                echo json_encode(["units" => $query]);

            }
            else
            {

                $query = $db -> fetch("SELECT questionID, question, isHide from question where unitID = :unitID",["unitID" => $value -> unitID]);

                echo json_encode(["questions" => $query]);

            }

        }

    }

}

?>