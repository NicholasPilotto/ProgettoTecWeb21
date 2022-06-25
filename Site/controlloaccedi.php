<?php
session_start();

use DB\Service;

require_once('backend/db.php');

$username = $_POST["username"];
$password = $_POST["password"];

$connessione = new Service();

$connessione->openConnection();

$log = $connessione->login($username, $password);

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
    // header("Location: index.php");
    $connessione->closeConnection(); // chiudo la connessione
    $_SESSION["info"] = $log->get_error_message();
    header("Location: accedi.php");
  }
} else {
  $connessione->closeConnection(); // chiudo la connessione
  $_SESSION["error"] = $log->get_error_message();
  header("Location: accedi.php");
}
