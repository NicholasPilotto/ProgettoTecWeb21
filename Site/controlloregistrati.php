<?php

session_start();

use DB\Service;

require_once('backend/db.php');

$errore = false;

$nome = $_POST["nome"];
$cognome = $_POST["cognome"];
$telefono = $_POST["telefono"];
$nascita = $_POST["nascita"];
$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];


$connessione = new Service();

$a = $connessione->openConnection();
if ($a) {

    $nomeCheck = (isset($nome) && preg_match('/^[A-Za-zàèùìòé\s]\w{2,20}$/', $nome));
    $cognomeCheck = (isset($cognome) && preg_match('/^[A-Za-zàèùìòé\s]\w{2,20}$/', $cognome));
    $usernameCheck = (isset($username) && preg_match('/^[A-Za-z\s]\w{2,10}$/', $username));
    $emailCheck = (isset($email) && preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email));
    $passwordCheck = (isset($password) && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$.,-;:<>!%*?&]{8,16}$/', $password));
    $telefonoCheck = (isset($telefono) && preg_match('/^[0-9]{10}$/', $telefono));

    // echo var_dump($nomeCheck) . "1 " . var_dump($cognomeCheck) . "2 " . var_dump($nascita) . "3 " . var_dump($usernameCheck) . "4 " . var_dump($emailCheck) . "5 " . var_dump($passwordCheck) . "6 " . var_dump($telefonoCheck);
    // echo var_dump($passwordCheck);
    // die();
    if ($nomeCheck && $cognomeCheck && isset($nascita) && $usernameCheck && $emailCheck && $passwordCheck && $telefonoCheck) {

        $utente = $connessione->signin($nome, $cognome, $nascita, $username, $email, $password, $telefono);

        if ($utente->ok()) {
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
            $connessione->closeConnection();
            $_SESSION["info"] = $utente->get_error_message();
            header("Location: registrati.php");
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
