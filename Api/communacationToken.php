<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    require_once "dbConnection.php";

    $value = json_decode(file_get_contents("php://input"));

    $db = new dbConnection();

    $query = $db -> fetch("select username, password, mail from tempPlayer where token = :token and activationCode = :activationCode ", ["token" => $value -> token, "activationCode" => $value -> activationCode]);

    if($query != false)
    {
        
        $db -> delete("tempPlayer",$query);
        $db -> insert("player", $query);

    } 
    else
        echo "hatalıke";


}

?>