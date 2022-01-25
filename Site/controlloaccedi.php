<?php
session_start();

use DB\Service;

require_once('backend/db.php');

$email = $_POST["email"];
$password = $_POST["password"];

$connessione = new Service();

$connessione->openConnection();

$log = $connessione->login($email, $password);

if ($log->ok()) {
  if (!$log->is_empty()) {

    $connessione->closeConnection(); // chiudo la connessione

    $_SESSION["Codice_identificativo"] = $log->get_result()[0]['codice_identificativo'];
            $_SESSION["Nome"] = $log->get_result()[0]['nome'];
            $_SESSION["Cognome"] = $log->get_result()[0]['cognome'];
            $_SESSION["Data_nascita"] = $log->get_result()[0]['data_nascita'];
            $_SESSION["Username"] = $log->get_result()[0]['username'];
            $_SESSION["Email"] = $log->get_result()[0]['email'];
            $_SESSION["Telefono"] = $log->get_result()[0]['telefono'];
    header("Location: index.php");
  } else {
    // nessun utente trovato
    header("Location: index.php");
  }
} else {
  $connessione->closeConnection(); // chiudo la connessione
  // messaggio errore: $log->get_error_message()
  header("Location: index.php?sbagliato=si");
}
