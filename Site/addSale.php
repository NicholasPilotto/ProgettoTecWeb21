<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

$isbn = $_POST["isbn"];
$sconto = $_POST["sconto"];
$inizio = $_POST["inizio"];
$fine = $_POST["fine"];

$connessione = new Service();
$a = $connessione->openConnection();
if ($a) {
  if (isset($isbn) && isset($sconto) && isset($inizio) && isset($fine)) {
    $data = $connessione->add_book_to_offers($isbn, $inizio, $fine, $sconto);
    if ($data->get_errno() != 0) {
      $_SESSION["error"] = $data->get_error_message();
    } else if ($data->get_errno() == 0) {
      $_SESSION["success"] = "Inserimento avvenuto con successo";
    }
  } else {
    $_SESSION["info"] = "Ci sono dei dati mancanti.";
  }
} else {
  $_SESSION["error"] = "Impossibile connettersi al sistema";
}
$connessione->closeConnection();
header("Location: applicaSconto.php?isbn=" . $isbn);
