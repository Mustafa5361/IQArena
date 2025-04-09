<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    require_once "dbConnection.php";

    $value = json_decode($_POST["value"]);

    $db = new dbConnection();

    $query = $db -> query("select username, password, mail from temporaryplayer where token = :token and activationCode = :activationCode ", ["token" => $value -> token, "activationCode" => $value -> activationCode]);
    
    if(true)
    {
        
    } 


}

?>