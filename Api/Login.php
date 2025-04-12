<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    
    error_reporting();

    require_once "dbConnection.php";
    require_once "token.php";
    require_once "activation.php";

    $value = json_decode($_POST["value"]); #file_get_contents("php://input")

    $db = new dbConnection();
    $token = new Token();
    $activation = new activation();

    if(!isset($value -> token))
    {

        if($value -> mail != "")
        {
            
            $query = $db -> fetch("select playerID from player where mail = :mail", ["mail" => $value -> mail]);

            if($query == false)
            {

                $createdToken = $token->activationCreateToken(); 
                $activationCode = $activation->CreateCode();
                
                $mail = new mail();

                $mail -> sendMail($value -> mail, $value -> username, "Activation Code", "<h1>Aktivasyon kodu</h1>\t\n<p>Oluşturulan Aktivasyon Kodunuz : $activationCode </p>");
            

                if($db -> insert("tempPlayer",["mail" => $value -> mail, "username" => $value -> username, "password" => $value -> password, "Token" => $createdToken, "activationCode" => $activationCode]) == 0)
                {
                    echo json_encode(["success" => true, "token" => $createdToken , "message" => "Registration completed successfully"]);
                }
                else
                {
                    echo json_encode(["success" => false, "token" => "" , "message" => "Failed registration"]);
                }

            }
            else
            {

                echo json_encode(["success" => false, "token" => "" , "message" => $value -> mail . " mail adresi mevcut"]);
                
            }

        }
        else
        {

            $sorgu = ["username" => $value -> username, "password" => $value -> password];

            $loginData = $db->fetch("select playerID From player where username = :username and password = :password", $sorgu);
            
            if($loginData == false)
            {
                echo json_encode(["success" => false, "token" => "" ,"message" => "username or password is incorrect"]);
            }
            else
            {
                echo json_encode(["success" => true, "token" => $token->CreateToken($loginData["playerID"]) , "message" => "login successful"]);
            }

        }
    }
    else
    {

        if(isset($value -> activationCode))
        {

            if($value -> resetPassword)
            {

                

            }
            else
            {

                $query = $db -> fetch("select username, password, mail from tempPlayer where token = :token and activationCode = :activationCode ", ["token" => $value -> token, "activationCode" => $value -> activationCode]);

                if($query != false)
                {
                    
                    $token = new Token();

                    $db -> delete("tempPlayer",$query);
                    $query = $db -> insert("player", $query);

                    echo json_encode(["success" => true, "token" => $token -> CreateToken($query), "message" => "login successful"]);

                } 
                else
                    echo json_encode(["success" => false, "token" => "", "message" => "login error"]);

            }
            
        }
        else
        {
            $db -> delete("tokens", ["token" => $value -> token]);
        }


    }


}

?>