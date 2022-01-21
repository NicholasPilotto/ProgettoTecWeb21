<?php

    session_start();

    use DB\Service;
    require_once('backend/db.php');

    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $connessione = new Service();

    $a = $connessione->openConnection();

    if($connessione->login($email,$password))
    {
        $utente = $connessione->get_utente_by_email($email);

        $_SESSION["nome"] = $utente[0]["Nome"];
        header("Location: index.php");
        die();
    }
    else
    {
        header("Location: index.php");
        die();
    }
?>