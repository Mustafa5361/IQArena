<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    require_once "dbConnection.php";
    require_once "createQuestion.php";

    $value = json_decode($_POST["value"]);

    $db = new dbConnection();

    if(isset($value -> point))
    {

        $playerID = $db -> fetch(
            "SELECT playerID from tokens
            where token = :token",
            [
                "token" => $token
            ]
        )["playerID"];

        $db -> query(
            "UPDATE player set point = point + :point
            where playerID = :playerID",
            [
                "point" => $value -> point,
                "playerID" => $playerID 
            ]
        );
    }
    else
    {
        if($value -> unitID == 0)
        {

            $query = $db -> fetchAll("SELECT unitID, unitName from unit",[]);

            echo json_encode(["units" => $query]);


        }
        else
        {

            $createQuestion = new createQuestion();
            $questions = $createQuestion -> selectRandomQuestionToUnit($value -> unitID);

            echo json_encode(["questions" => $questions]);

        }
    }

}

?>