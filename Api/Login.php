<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    error_reporting();

    require_once "dbConnection.php";

    $value = json_decode($_POST["value"]);

    $sqlConn = new dbConnection();


    if($value -> mail != "")
    {

        if($sqlConn -> SingIn($value -> mail, $value -> username, $value -> password))
        {
            echo json_encode(["success" => true, "message" => "Registration completed successfully"]);
        }
        else
        {
            echo json_encode(["success" => false, "message" => "Failed registration"]);
        }

    }
    else
    {

        $loginData = $sqlConn->login($value -> username, $value -> password);
        
        if($loginData == false)
        {
            echo json_encode(["success" => false, "message" => "username or password is incorrect"]);
        }
        else
        {
            echo json_encode(["success" => true, "message" => "login successful"]);
        }

    }


}

?>