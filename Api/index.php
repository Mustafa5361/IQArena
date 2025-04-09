<?php

#test için kullanılan bir script

require_once "dbConnection.php";

$value = file_get_contents("php://input");

$value = json_decode($value);

$db = new dbConnection();

$result = $db -> insert("player", ["email" => $value -> mail, "username" => $value -> username, "password" => $value -> password]);

echo json_encode($result)

?>
