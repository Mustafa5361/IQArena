<?php

echo strlen("394e57bad8e512fde504b9326ad5e19a83f363f237c22908e0fff9137ff66ef5");

require_once "dbConnection.php";

$value = file_get_contents("php://input");

$value = json_decode($value);

$db = new dbConnection();

$result = $db -> insert("player", ["email" => $value -> mail, "username" => $value -> username, "password" => $value -> password]);

echo json_encode($result)

?>
