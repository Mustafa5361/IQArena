<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    error_reporting();

    require_once "dbConnection.php";
    require_once "token.php";

    $value = json_decode(file_get_contents("php://input")); #file_get_contents("php://input")

    $db = new dbConnection();
    $token = new Token();

    if(!isset($value -> token))
    {
        if($value -> mail != "")
        {

            if($db -> insert("player", ["email" => $value -> mail, "username" => $value -> username, "password" => $value -> password]))
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

            $sorgu = ["username" => $value -> username, "password" => $value -> password];

            $loginData = $db->fetch("select playerID From player where username = :username and password = :password", $sorgu);
            
            if($loginData == false)
            {
                echo json_encode(["success" => false, "token" => "" ,"message" => "username or password is incorrect"]);
            }
            else
            {
                echo json_encode(["success" => true, "token" => $token -> CreateToken($loginData["playerID"]) , "message" => "login successful"]);
            }

        }
    }
    else
        $token -> DelateToken($value -> token);


}

?>