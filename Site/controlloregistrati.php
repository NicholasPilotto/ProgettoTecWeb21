<?php

session_start();

use DB\Service;

require_once('backend/db.php');

$errore = false;

$nome = isset($_POST["nome"]) ? $_POST["nome"] : $errore = true;
$cognome = isset($_POST["cognome"]) ? $_POST["cognome"] : $errore = true;
$telefono = isset($_POST["telefono"]) ? $_POST["telefono"] : $errore = true;
$nascita = isset($_POST["nascita"]) ? $_POST["nascita"] : $errore = true;
$username = isset($_POST["username"]) ? $_POST["username"] : $errore = true;
$email = isset($_POST["email"]) ? $_POST["email"] : $errore = true;
$password = isset($_POST["password"]) ? $_POST["password"] : $errore = true;

if (!$errore) {

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

        echo($utente->get_errno());

        header("Location: registrati.php");
        die();
    }
} else {
    $_SESSION["error"] = "I campi non sono stati inseriti correttamente";
    $connessione->closeConnection();
    header("Location: registrati.php");
}
