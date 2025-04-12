<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    require_once "dbConnection.php";
    require_once "token.php";

    $value = json_decode($_POST["value"]);

    


}

?>