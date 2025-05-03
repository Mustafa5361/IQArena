<?php
// questions(question +, answer+, difficultylevel+, ishide?, correctanswer)  unitID-,
// ishideing(quesionID, isHide)

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    require_once "createQuestion.php";

    $value = json_decode($_POST["value"]);
    $db = new dbConnection();
   
    $playerID = $db -> fetch("SELECT playerID FROM tokens where token = :token",
    [
        "token" => $value -> token
    ]["playerID"]);

    if($playerID == 1)
    {
        $createQuestion = new createQuestion();
        
        if(isset($value -> isHideing))
        {
            
            foreach($value -> isHideing as $hide)
            {
                $createQuestion -> isHideing(
                    $hide -> quesionID,
                    $hide -> isHide
                );
            }
            
        }
        else
        {

            $control = $db -> fetch("SELECT unitID FROM unit where unitName = : unitName",
            [
                "unitName" => $value -> unitName
            ]);
            // unit yoksa unityi oluşturuyoruz
            if($control == False)
            {
                $db -> insert("unit", ["unitName" => $value -> unit]);
            }

            

            foreach($value ->questions as $question)
            {

                $createQuestion -> createQuestion(
                    $value -> unit,
                    $question -> question,
                    $question -> answerA,
                    $question -> answerB,
                    $question -> answerC,
                    $question -> answerD,
                    $question -> currentAnswer,
                    $question -> difficultyLevel
                );

            }
        }

    }
    else
    {
        die("ERROR: You are not admin.");
    }

}
else
{

    //eror kodu
}












?>