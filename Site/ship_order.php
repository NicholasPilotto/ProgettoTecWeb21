<?php
if (!isset($_SESSION)) {
  session_start();
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$user = $_SESSION["Codice_identificativo"];


if (isset($user) && $user != "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
  $connessione = new Service();
  $a = $connessione->openConnection();


  if ($a) {
    $order = $_GET["ordine"];
    $orderCheck = isset($order);
    if ($orderCheck) {
      // echo $order;
      $data = $connessione->ship_order($order);
      if ($data->ok()) {
        $_SESSION["success"] = "Ordine spedito correttamente";
      } else {
        $_SESSION["info"] = $data->get_error_message();
      }
    } else {
      $_SESSION["info"] = "Dati mancanti o non corretti.";
    }
  } else {
    $_SESSION["error"] = "Impossibile connettersi al sistema.";
  }
} else {
  $_SESSION["error"] = "Non possiedi i permessi necessari.";
}

header("Location: orderToShip.php");
