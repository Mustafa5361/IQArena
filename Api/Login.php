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
    $mail = new mail();
    $activation = new activation();

    if(!isset($value -> token)) // gelen bir token varsa logout işlemi
    {

        if($value -> mail != "") //mail varsa signin veya şifremi unuttum
        { 
            if(isset($value -> username)) //userename varsa signin
            {

                $query = $db -> fetch("select playerID from player where mail = :mail and isDeleted = :isDeleted", ["mail" => $value -> mail,  "isDeleted" => false]);

                if($query == false) 
                {

                    $createdToken = $token->activationCreateToken(); 
                    $activationCode = $activation->CreateCode();
                    
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

                if(isset($value -> password))
                    {

                        $query = $db->fetch(
                            "SELECT player.playerID 
                             FROM activationcode 
                             JOIN player ON activationcode.playerID = player.playerID 
                             WHERE mail = :mail AND activationCode = :activationCode
                             and isDeleted = false",
                            [
                                "mail" => $value->mail,
                                "activationCode" => $value->activationCode
                            ]
                        );

                        if($query != false)
                        {

                            $query = $db -> query(
                                "Update player set password = :password where mail = :mail and isDeleted = false",
                                ["password" => $value -> password, "mail" => $value -> mail]
                            );
                            if($query != false)
                            {
                                $query = $db -> fetch("select playerID from player where mail = :mail and isDeleted = false",["mail" => $value -> mail]);
                                echo json_encode(["success" => true, "token" => "" ,"message" => "password has been changed"]);
                                $db -> query("Delete from activationcode where playerID = :playerID", ["playerID" => $query["playerID"]]);
                            }
                            else 
                            {
                                echo json_encode(["success" => false, "token" => "" ,"message" => "password has not been changed"]);
                            }

                        }

                    }
                    else if (isset($value -> activationCode))
                    {

                        $query = $db->fetch(
                            "SELECT player.playerID 
                             FROM activationcode 
                             JOIN player ON activationcode.playerID = player.playerID 
                             WHERE mail = :mail AND activationCode = :activationCode
                             and isDeleted = :isdeleted",
                            [
                                "mail" => $value->mail,
                                "activationCode" => $value->activationCode,
                                "isDeleted" => false
                            ]
                        );

                        if($query != false)
                        {

                            echo json_encode(["success" => true, "token" => "" ,"message" => "activation code is correct"]);

                        }
                        else
                        {
                            $db -> query("update activationcode set try = try - 1 where mail = :mail",["try"]);
                            echo json_encode(["success" => false, "token" => "" ,"message" => "activation code is incorrected"]);
                            
                        }


                    }
                    else
                    {

                        $query = $db -> fetch("select playerID,username from player where mail  = :mail", ["mail" => $value -> mail] );
                        if($query != false)
                        {
                            $activationCode = $activation -> createCode();
                            $db -> insert("activationcode",["playerID" => $query["playerID"], "activationCode" => $activationCode]);
                            $mail -> sendMail($value -> mail, $query["username"], "reset password", "<h1>Activation Code</h1> <p>Aktivasyon kodunuz: $activationCode </p>");
                            echo json_encode(["success" => true, "token" => "" ,"message" => "Your activation code has been sent."]);
                            

                        }
                        

                    }


                
            }

        }
        else // login
        {

            $sorgu = ["username" => $value -> username, "password" => $value -> password, "isDeleted" => false];

            $loginData = $db->fetch("select playerID From player where username = :username and password = :password and isDeleted = :isDeleted", $sorgu);
            
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

        if(isset($value -> activationCode)) // hesabı kalıcı bir şekilde oluşturmma
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
        else
        {
            $db -> delete("tokens", ["token" => $value -> token]);
        }


    }


}

?>