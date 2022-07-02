<?php

session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$errore = false;

$connessione = new Service();
$a = $connessione->openConnection();

$citta = $_POST["citta"];
$cap = $_POST["cap"];
$via = $_POST["via"];
$num_civico = $_POST["num_civico"];
$user = $_SESSION["Codice_identificativo"];

if ($a) {
  $cittaCheck = (isset($_POST["citta"]) && preg_match('/^[A-Za-zàèùìòé\s ]{2,20}$/', $citta));
  $capCheck = (isset($_POST["cap"]) && preg_match('/^[0-9]{5}$/', $cap));
  $viaCheck = (isset($_POST["via"]) && preg_match('/^[A-Za-zàèùìòé\s]{2,20}$/', $via));
  $num_civicoCheck = (isset($_POST["num_civico"]) && preg_match('/^([1-9][0-9]*)(\/[a-zA-Z])*?/', $num_civico));
  $userCheck = isset($_SESSION["Codice_identificativo"]);

  if ($cittaCheck && $capCheck && $viaCheck && $num_civicoCheck && $userCheck) {

    $data = $connessione->insert_address($user, $via, $citta, $cap, $num_civico);
    if ($data->get_errno() != 0) {
      $_SESSION["info"] = $data->get_error_message();
    } else if ($data->get_errno() == 0) {
      $_SESSION["success"] = "Inserimento avvenuto con successo";
    }
  } else {
    $_SESSION["info"] = "Dati mancanti o non corretti.";
  }
  $connessione->closeConnection();
} else {
  $_SESSION["error"] = "Impossibile connettersi al sistema.";
  $connessione->closeConnection();
}
header("Location: aggiungiIndirizzo.php");
