<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";


$order = $_REQUEST["orderDelete"];
$user = $_SESSION["Codice_identificativo"];

if (isset($user)) {
  if (isset($order)) {
    $connessione = new Service();
    $a = $connessione->openConnection();
    if ($a) {
      $delete = $connessione->delete_order($order, $user);
      if ($delete->ok()) {
        $_SESSION["success"] = "Ordine eliminato correttamente";
        header("Location: ordini.php");
      } else {
        $_SESSION["info"] = $delete->get_error_message();
        header("Location: infoOrdine.php");
      }
    } else {
      $_SESSION["error"] = "Impossibile connettersi al sistema";
      header("Location: infoOrdine.php");
    }
  } else {
    $_SESSION["info"] = "Nessun ordine da eliminare";
    header("Location: ordini.php");
  }
} else {
  $_SESSION["error"] = "La sessione sembra corrotta";
  header("Location: ordini.php");
}
