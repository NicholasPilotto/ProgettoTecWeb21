<?php

session_start();

use DB\Service;

require_once('backend/db.php');

$errore = false;

$value = isset($_POST["valutazione"]) ? $_POST["valutazione"] : $errore = true;
$comment = isset($_POST["commento"]) ? $_POST["commento"] : $errore = true;
$isbn = isset($_SESSION["isbnReview"]) ? $_SESSION["isbnReview"] : $errore = true;
$utente = isset($_SESSION["Codice_identificativo"]) ? $_SESSION["Codice_identificativo"] : $errore = true;

if (!$errore) {

  $connessione = new Service();

  $a = $connessione->openConnection();

  $rev = $connessione->insert_review($utente, $isbn, $value, $comment);

  if ($rev->ok()) {
    if (!$rev->is_empty()) {
      $connessione->closeConnection();
      unset($_SESSION["isbnRevire"]);

      header("Location: index.php");
    } else {
      $_SESSION["error"] = $utente->get_error_message();
      $connessione->closeConnection();
    }
  } else {
    $_SESSION["error"] = $utente->get_error_message();
    $connessione->closeConnection();

    header("Location: lasciaRecensione.php?isbn=" . $isbn);
  }
} else {
  $_SESSION["error"] = "I campi non sono stati inseriti correttamente";
  $connessione->closeConnection();
  header("Location: lasciaRecensione.php?isbn=" . $isbn);
}
