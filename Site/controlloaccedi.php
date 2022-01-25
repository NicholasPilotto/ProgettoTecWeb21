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

    $utente = $log->get_result()[0];
    $_SESSION["Codice_identificativo"] = $utente["Codice_identificativo"];
    $_SESSION["Nome"] = $utente["nome"];
    $_SESSION["Cognome"] = $utente["cognome"];
    $_SESSION["Data_nascita"] = $utente["data_nascita"];
    $_SESSION["Username"] = $utente["username"];
    $_SESSION["Email"] = $utente["email"];
    $_SESSION["Telefono"] = $utente["telefono"];

    $connessione->closeConnection(); // chiudo la connessione
    header("Location: index.php");
  } else {
    // nessun utente trovato
    $connessione->closeConnection();
    header("Location: index.php?vuoto=si");
  }
} else {
  $connessione->closeConnection(); // chiudo la connessione
  // messaggio errore: $log->get_error_message()
  header("Location: index.php?sbagliato=si");
}
