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

    $_SESSION["Codice_identificativo"] = $utente[0]["Codice_identificativo"];
    $_SESSION["Nome"] = $utente[0]["Nome"];
    $_SESSION["Cognome"] = $utente[0]["Cognome"];
    $_SESSION["Data_nascita"] = $utente[0]["Data_nascita"];
    $_SESSION["Username"] = $utente[0]["Username"];
    $_SESSION["Email"] = $utente[0]["Email"];
    $_SESSION["Telefono"] = $utente[0]["Telefono"];

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
