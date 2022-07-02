<?php

session_start();

use DB\Service;

require_once('backend/db.php');

$errore = false;

$value = $_POST["valutazione"];
$comment = $_POST["commento"];
$isbn = $_SESSION["isbnReview"];
$utente = $_SESSION["Codice_identificativo"];

$valueCheck = (isset($value) && preg_match('/^[0-5]$/', $value));
$commentCheck = (isset($comment) && preg_match('/^[a-zA-Z0-9 òàùèé()\'?.,!-]{10,500}$/', $comment));
$isbnCheck = (isset($isbn) && preg_match('/^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/', $isbn));
$utenteCheck = (isset($utente));



if ($valueCheck && $commentCheck && $isbnCheck && $utenteCheck) {

  $connessione = new Service();

  $a = $connessione->openConnection();
  if ($a) {

    $rev = $connessione->insert_review($utente, $isbn, $value, $comment);

    if ($rev->ok()) {
      if (!$rev->is_empty()) {

        if ($rev->get_errno() != 0) {
          $_SESSION["info"] = "Hai già recensito questo libro.";
          $connessione->closeConnection();

          header("Location: lasciaRecensione.php?isbn=" . $isbn);
        }
        $connessione->closeConnection();
        unset($_SESSION["isbnRevire"]);

        $_SESSION["success"] = "Recensione inserita con successo.";

        header("Location: lasciaRecensione.php?isbn=" . $isbn);
      } else {
        $_SESSION["info"] = $utente->get_error_message();
        $connessione->closeConnection();
      }
    } else {
      if ($rev->get_errno() != 0) {
        $_SESSION["info"] = "Hai già recensito questo libro.";
        $connessione->closeConnection();

        header("Location: lasciaRecensione.php?isbn=" . $isbn);
      }
      $_SESSION["error"] = "Impossibile connettersi al sistema.";
      $connessione->closeConnection();

      header("Location: lasciaRecensione.php?isbn=" . $isbn);
    }
  } else {
    $_SESSION["error"] = "Impossibile connettersi al sistema.";
    $connessione->closeConnection();

    header("Location: lasciaRecensione.php?isbn=" . $isbn);
  }
} else {
  $_SESSION["info"] = "Non tutti i campi sono inseriti completamente";
  header("Location: lasciaRecensione.php?isbn=" . $isbn);
}
