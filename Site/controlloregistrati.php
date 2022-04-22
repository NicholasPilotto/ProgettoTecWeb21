<?php

session_start();

use DB\Service;

require_once('backend/db.php');

$nome = $_POST["nome"];
$cognome = $_POST["cognome"];
$telefono = $_POST["telefono"];
$nascita = $_POST["nascita"];
$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];

$connessione = new Service();

$a = $connessione->openConnection();

$utente = $connessione->signin($nome, $cognome, $nascita, $username, $email, $password, $telefono);

if ($utente->ok()) {
    if (!$utente->is_empty()) {
        $connessione->closeConnection();

        $_SESSION["Codice_identificativo"] = $utente->get_result()[0]['codice_identificativo'];
        $_SESSION["Nome"] = $utente->get_result()[0]['nome'];
        $_SESSION["Cognome"] = $utente->get_result()[0]['cognome'];
        $_SESSION["Data_nascita"] = $utente->get_result()[0]['data_nascita'];
        $_SESSION["Username"] = $utente->get_result()[0]['username'];
        $_SESSION["Email"] = $utente->get_result()[0]['email'];
        $_SESSION["Telefono"] = $utente->get_result()[0]['telefono'];

        header("Location: index.php");
        die();
    } else {
        echo "test";
    }
} else {
    $_SESSION["error"] = $utente->get_error_message();
    $connessione->closeConnection();
    header("Location: registrati.php");
    die();
}
