<?php

class dbConnection
{

    function Connection()
    {

        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "iqarenadb";

        $conn = mysqli_connect($host, $username, $password, $database);

        if ($conn -> connect_error)
        {
            die("Database Connection Error : " . $conn -> connect_error);
        }

        return $conn;

    }

    function Close($conn)
    {
        mysqli_close($conn);
    }

    function Login($username, $password)
    {
        
        $sorgu = "select playerID from player where username = '" . $username . "' and password = '" . $password . "' and isDeleted = 0";

        $conn = $this -> Connection();

        $resault = $conn -> query($sorgu);
        
        $this -> Close($conn);

        if($resault -> num_rows > 0)
        {
            $row = $resault->fetch_object();
            return $row->playerID;
        }
        else
        {
            return false;
        }

    }

    function SingIn($mail, $username, $password)
    { 
        $sorgu = "Insert into player (email, username, password) values ('$mail', '$username', '$password');";

        $conn = $this -> Connection();

        $resault = $conn -> prepare($sorgu);

        if ($resault->execute())
        {

            $this -> Close($conn);
            return true;

        }
        else
        {
            
            $this -> Close($conn);
            return false;

        }

    }

    function TokenControl($Token)
    {
        $sorgu = "select TokenID from Tokens where Token = " . $Token;
        $conn = $this -> Connection();
        $resault = $conn -> query($sorgu);
        $this -> Close($conn);

        if($resault -> num_rows >0)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    function SaveToken($playerID, $token)
    { 
        $sorgu = "Insert into tokens (playerID, token) values ('$playerID', '$token');";

        $conn = $this -> Connection();

        $resault = $conn -> prepare($sorgu);

        if ($resault->execute())
        {

            $this -> Close($conn);
            return true;

        }
        else
        {
            
            $this -> Close($conn);
            return false;

        }

    }

}

?>