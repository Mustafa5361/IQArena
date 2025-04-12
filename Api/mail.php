<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";   

class mail
{

    private $mail;

    public function __construct()
    {

        $this -> mail = new PHPMailer();
        $this -> mail -> isSMTP();
        $this -> mail -> SMTPSecure = 'tls';
        $this -> mail -> Host = 'smtp.gmail.com';
        $this -> mail -> Port = 587;
        $this -> mail -> SMTPSecure = 'tls';
        $this -> mail -> SMTPAuth = true;
        $this -> mail -> SMTPKeepAlive = true;
        $this -> mail -> CharSet = 'UTF-8';
        $this -> mail -> Username = "mustafatorpi5353@gmail.com";
        $this -> mail -> Password = "ycxcnqaaaowqydwn";

        $this -> mail -> setFrom("mustafatorpi5353@gmail.com", "Activation Code");

    }

    public function sendMail($mail, $username, $title, $massage)
    {

        $this -> mail -> isHTML(true);
        $this -> mail -> addAddress($mail, $username);
        $this -> mail -> Subject = $title;
        $this -> mail -> Body = $massage;

        if ($this -> mail -> send())
            return true;
        else
            return $this -> mail -> ErrorInfo;

    }

}

?>