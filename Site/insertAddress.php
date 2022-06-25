<?php

session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$errore = false;

$connessione = new Service();
$connessione->openConnection();

$errore = !($connessione->openConnection());
if (!$errore) {

  $citta = isset($_POST["citta"]) ? $_POST["citta"] : null;
  $cap = isset($_POST["cap"]) ? $_POST["cap"] : null;
  $via = isset($_POST["via"]) ? $_POST["via"] : null;
  $num_civico = isset($_POST["num_civico"]) ? $_POST["num_civico"] : null;
  $user = $_SESSION["Codice_identificativo"];

  $data = $connessione->insert_address($user, $via, $citta, $cap, $num_civico);
  if ($data->get_errno() != 0) {
    $_SESSION["error"] = $data->get_error_message();
  } else if ($data->get_errno() == 0) {
    $_SESSION["success"] = "Inserimento avvenuto con successo";
  }
  header("Location: aggiungiIndirizzo.php");
} else {
  $paginaHTML = graphics::getPage("aggiungiIndirizzo_php.html");
  $paginaHTML = str_replace("</alert>", "<span class='alert error'>Qualcosa è andato storto</span>", $paginaHTML);
  echo $paginaHTML;
}
