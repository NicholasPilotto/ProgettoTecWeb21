<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

$isbn = $_POST["isbn"];
$sconto = $_POST["sconto"];
$inizio = $_POST["inizio"];
$fine = $_POST["fine"];

$isbnCheck = (isset($isbn)) && preg_match('/^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/', $isbn);
$scontoCheck = (isset($sconto) && preg_match('/^[1-9][0-9]*$/', $sconto));
$inizioCheck = (isset($inizio));
$fineCheck = (isset($fine));

$connessione = new Service();
$a = $connessione->openConnection();
if ($a) {
  if ($isbnCheck && $scontoCheck && $inizioCheck && $fineCheck) {
    $data = $connessione->add_book_to_offers($isbn, $inizio, $fine, $sconto);
    if ($data->get_errno() != 0) {
      $_SESSION["error"] = $data->get_error_message();
    } else if ($data->get_errno() == 0) {
      $_SESSION["success"] = "Inserimento avvenuto con successo";
    }
  } else {
    $_SESSION["info"] = "Dati mancanti o non corretti.";
  }
} else {
  $_SESSION["error"] = "Impossibile connettersi al sistema";
}
$connessione->closeConnection();
header("Location: applicaSconto.php?isbn=" . $isbn);
