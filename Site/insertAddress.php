<?php

session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$errore = false;

$connessione = new Service();
$a = $connessione->openConnection();

if ($a) {
  $citta = isset($_POST["citta"]) ? $_POST["citta"] : null;
  $cap = isset($_POST["cap"]) ? $_POST["cap"] : null;
  $via = isset($_POST["via"]) ? $_POST["via"] : null;
  $num_civico = isset($_POST["num_civico"]) ? $_POST["num_civico"] : null;
  $user = $_SESSION["Codice_identificativo"];

  if (isset($citta) && isset($cap) && isset($via) && isset($num_civico) && isset($user)) {

    $data = $connessione->insert_address($user, $via, $citta, $cap, $num_civico);
    if ($data->get_errno() != 0) {
      $_SESSION["info"] = $data->get_error_message();
    } else if ($data->get_errno() == 0) {
      $_SESSION["success"] = "Inserimento avvenuto con successo";
    }
  } else {
    $_SESSION["info"] = "Dati mancanti.";
  }
} else {
  $_SESSION["error"] = "Impossibile connettersi al sistema.";
}
header("Location: aggiungiIndirizzo.php");
