<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    $value = $_POST["value"];

    $value = json_decode($value);

    if($value -> usermame == "deneme" && $value -> password = "deneme")
    {
        echo json_encode(["success" => true, "message" => "login successful"]);
    }
    else
    {
        echo json_encode(["success" => false, "message" => "username or password is incorrect"]);
    }

}

?>