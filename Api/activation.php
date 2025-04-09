<?php
// register.php ingin

require_once 'dbConnection.php'; // PHPMailer ayarları
class activation
{

    private $db ;

    public function __construct()
    {
        $this -> db = new dbConnection();
    }

    function createCode()
    {
        
        $code = bin2hex(random_bytes(3)); 
        return $code;

    }
    
    function codeControl()
    {

    }




}

?>