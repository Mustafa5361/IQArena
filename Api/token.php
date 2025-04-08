<?php

require_once "dbConnection.php";

class Token
{

    function CreateToken($playerID)
    {   

        $mysql = new dbConnection();

        do
        {

            $token = bin2hex(random_bytes(64));

        }while($mysql -> TokenControl($token));
        
        $mysql -> SaveToken($playerID, $token);

        return $token;

    }


}

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    error_reporting();

    $value = $_POST ["value"];
    $mysql = new dbConnection();
    
    if($mysql -> TokenControl($value))
    {
        echo json_encode(["success" => true, "token" => $value,"message" => "login successful"]);
        
    }


}

?>