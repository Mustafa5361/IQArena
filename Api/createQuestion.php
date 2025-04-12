<?php
require_once "dbConnection.php";

class createQuestion
{

    private $db;

    function __constructor()
    {
        $this -> db=  new dbConnection();
    }

    function createQuestion($question)
    {
        
        $unit = $this -> db -> insert("unit",$question[0]);
        
        unset($question[0]);

        foreach($question  as $kay => $value)
        {
            
            array_push($value, ["unit" => $unit]);
            $this -> db -> insert("question", $question);

        }

        return true;

    }

    function selectRandomQuestion()
    {
        
        $difficultyLevel = 0;
        
        for ($i = 1; $i <= 10; $i++)
        {

            $difficultyLevelNow = $difficultyLevel;
            $ust = false;
            $questions = [];

            do{

                if($ust || $difficultyLevel <= 0)
                {
                    $difficultyLevelNow++;
                    $ust = true;
                }
                else
                    $difficultyLevel--;

                $query = $this -> db -> fetch("select questionID from unit where difficultyLevel = :difficultyLevel order by rand();",["difficultyLevel" => $difficultyLevel]);

                array_push($questions, $query);

            }while($query == false);

            $difficultyLevel++; 

        }  

    }

}    

    
    
    
    
    
    
    
    
    
    
    
?>