<?php

require_once "createQuestion.php";

$question = new createQuestion();

$questions = $question -> selectRandomQuestion();

print_r($questions);

?>
