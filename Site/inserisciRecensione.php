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
  if ($a) {
    if (isset($utente) && isset($isbn) && isset($value) && isset($comment)) {
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
      $_SESSION["info"] = "Non tutti i campi sono inseriti completamente";
      $connessione->closeConnection();

      header("Location: lasciaRecensione.php?isbn=" . $isbn);
    }
  } else {
    $_SESSION["error"] = "Impossibile connettersi al sistema.";
    $connessione->closeConnection();

    header("Location: lasciaRecensione.php?isbn=" . $isbn);
  }
} else {

  $_SESSION["error"] = "Impossibile connettersi al sistema.";
  $connessione->closeConnection();
  header("Location: lasciaRecensione.php?isbn=" . $isbn);
}
