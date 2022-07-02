<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

$utente = $_POST["idUtente"];
$isbn = $_POST["isbn"];

$utenteCheck = (isset($utente));
$isbnCheck = (isset($isbn) && preg_match('/^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/', $isbn));

echo $isbn;

if ($utenteCheck && $isbnCheck) {
  $connessione = new Service();
  $a = $connessione->openConnection();

  if ($a) {
    $data = $connessione->delete_review($utente, $isbn);
    if ($data->ok() && $data->get_errno() == 0) {
      $_SESSION["success"] = "Recensione eliminata correttamente";
    } else {
      $_SESSION["info"] = $data->get_error_message();
    }
  } else {
    $_SESSION["error"] = "Impossibile connettersi al sistema.";
  }
} else {
  $_SESSION["info"] = "Dati mancanti o non corretti.";
}

header("Location: recensioni.php");
