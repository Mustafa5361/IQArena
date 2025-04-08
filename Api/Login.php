<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    error_reporting();

    require_once "dbConnection.php";
    require_once "token.php";

    $value = json_decode($_POST["value"]); #file_get_contents("php://input")

    $sqlConn = new dbConnection();

    $token = new Token();

    if($value -> mail != "")
    {

        if($sqlConn -> SingIn($value -> mail, $value -> username, $value -> password))
        {
            echo json_encode(["success" => true, "token" => "" , "message" => "Registration completed successfully"]);
        }
        else
        {
            echo json_encode(["success" => false, "token" => "" , "message" => "Failed registration"]);
        }

    }
    else
    {

        $loginData = $sqlConn->login($value -> username, $value -> password);
        
        if($loginData == false)
        {
            echo json_encode(["success" => false, "token" => "" ,"message" => "username or password is incorrect"]);
        }
        else
        {
            echo json_encode(["success" => true, "token" => $token -> CreateToken($loginData) , "message" => "login successful"]);
        }

    }


}

?>