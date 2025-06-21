<?php
//veri tabanına unit tablosuna visibility ve accessibility eklenecek tüm sorgular bu eklenenlere göre düzenlenicek
//ve bu sayfa içerisindegelen unit ve questionların visibility ve accessibility degerleri düzenlenicek.
//questionda sadece isHide düzenlenicek. visibility ve accessibility questionda yok.


if($_SERVER["REQUEST_METHOD"] == "POST")
{
    require_once "dbConnection.php";
    
    $value = json_decode($_POST["value"]);

    if ($value === null) 
    {
        echo json_encode(["status" => "error", "message" => "Invalid JSON."]);
        exit;
    }

    $db = new dbConnection();

     // Units kontrolü ve dizi mi
     if (isset($value->units) && is_array($value->units)) 
     {
        foreach ($value->units as $unit) 
        {
            if (isset($unit->unitID, $unit->visibility, $unit->accessibility)) 
            {
                $db->update("unit", 
                    [
                        "visibility" => $unit->visibility,
                        "accessibility" => $unit->accessibility
                    ],
                    [
                        "unitID" => $unit->unitID
                    ]
                );
            }
        }
        echo json_encode(["status" => "success", "message" => "Units updated."]);
        exit;
    }

    // Questions kontrolü
    elseif (isset($value->questions) && is_array($value->questions)) 
    {
        foreach ($value->questions as $question) {
            if (isset($question->questionID, $question->isHide)) 
            {
                $db->update("question", 
                    [
                        "isHide" => $question->isHide
                    ],
                    [
                        "questionID" => $question->questionID
                    ]
                );
            }
        }
        echo json_encode(["status" => "success", "message" => "Questions updated."]);
        exit;
    }
    else
    {
    // Ne units ne de questions varsa:
    echo json_encode(["status" => "error", "message" => "Neither units nor questions provided."]);
    }

}

?>