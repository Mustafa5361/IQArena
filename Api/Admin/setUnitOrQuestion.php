<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    $value = json_decode($_POST["value"]);

    //veri tabanına unit tablosuna visibility ve accessibility eklenecek tüm sorgular bu eklenenlere göre düzenlenicek
    //ve bu sayfa içerisindegelen unit ve questionların visibility ve accessibility degerleri düzenlenicek.
    //questionda sadece isHide düzenlenicek. visibility ve accessibility questionda yok.

}

?>