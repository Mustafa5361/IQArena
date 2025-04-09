<?php

require_once "dbConnection.php";

class Token
{

    private $db;

    public function __construct()
    {
        $this -> db = new dbConnection();
    }

    function CreateToken($playerID)
    {   

        do
        {

            $token = bin2hex(random_bytes(32));

        }while($this -> db -> fetch("select tokenID from tokens where Token = :token", ["token" => $token]) != false);
        
        $this -> db -> insert("tokens",["playerID" => $playerID, "Token" => $token]);

        return $token;

    }

    function TokenControl($token)
    {
        $this -> db -> fetch("select tokenID from tokens where Token = :token", ["token" => $token]);
    }

    function DelateToken($token)
    {
        $this -> db -> delete("tokens", ["token" => $token]);
    }

}

?>