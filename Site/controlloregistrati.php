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


$connessione = new Service();

$a = $connessione->openConnection();
if ($a) {

    if (isset($nome) && isset($cognome) && isset($nascita) && isset($username) && isset($email) && isset($password) && isset($telefono)) {

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
            } else {
                $_SESSION["info"] = $utente->get_error_message();
                header("Location: registrati.php");
            }
        } else {
            $connessione->closeConnection();
            $_SESSION["info"] = $utente->get_error_message();
        }
    } else {
        $connessione->closeConnection();
        $_SESSION["info"] = "Non tutti i campi sono stati inseriti correttamente";
        header("Location: registrati.php");
    }
} else {
    $_SESSION["error"] = "Impossibile connettersi al sistema";
    header("Location: registrati.php");
}
