<?php
require_once "dbConnection.php";

class createQuestion
{

    private $db;

    function __construct()
    {
        $this -> db = new dbConnection();
    }

    function createQuestion($unit, $question, $answerA, $answerB, $answerC, $answerD, $currentAnswer, $difficultyLevel)
    {
        $this -> db -> fetch("SELECT unitID FROM unit where unitName = :unitName",
        [
            "unitName" => $unit 
        ]);
        $this -> db -> insert("question", 
        [
            "question" => $question,
            "answerA" => $answerA,
            "answerB" => $answerB,
            "answerC" => $answerC,
            "answerD" => $answerD,
            "currentAnswer" => $currentAnswer,
            "difficultyLevel" => $difficultyLevel,
        ]    
        );

        return true;

    }
    

    function selectRandomQuestion()
    {

        $difficultyLevel = 0;
        $questions = [];

        for ($i = 1; $i <= 10; $i++)
        {   

            $difficultyLevel++;

            $up = false;
            $difficultyLevelNow = $difficultyLevel;
            
            do 
            {
                
                $sorgu = "";
                if(!empty($questions))
                    foreach($questions as $question)
                    {
                        
                        $sorgu .= (" and questionID != " . $question["questionID"]);

                    }

                // Soruyu seçmek için sorguyu çalıştırıyoruz
                $query = $this->db->fetch(
                    "SELECT questionID FROM question 
                    WHERE difficultyLevel = :difficultyLevel and isHide = 0 $sorgu ORDER BY rand()", 
                    ["difficultyLevel" => $difficultyLevelNow]
                );
                
                if ($difficultyLevelNow <= 1 || $up) 
                {
                    $up = true;
                    $difficultyLevelNow++; // Zorluk seviyesini arttırıyoruz
                }
                else 
                {
                    $difficultyLevelNow--; // Zorluk seviyesini azaltıyoruz
                }

            } while ($query == false); // Eğer soru bulunmazsa döngü devam eder

            array_push($questions, $query);
            
        }
        
        return $questions; // Eğer hiçbir soru seçilemezse null döner

    }


}    

    
    
    
    
    
    
    
    
    
    
    
?>