<?php

use DB\response_manager;
use DB\Service;

require_once('db.php');
require_once('response_manager.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_GET["f"] == 'a') {
  $connessione = new Service();
  $a = $connessione->openConnection();
  // $aux = $connessione->insert_book(9788817108331, "Io sono il calcio", 3030, 300, 12.5, 10, "2018-10-03", "");

  // $aux = $connessione->get_addresses(1000000000);
  $aux = $connessione->get_bestsellers();
  print_r(json_encode($aux->get_result()));
  echo var_export($aux->is_empty());
  echo $aux->get_error_message();
  echo var_export($aux->get_errno());
  echo var_export($aux->ok());
  echo $aux->get_error_message_mysqli();

  $connessione->closeConnection();
}


// $idGenere = $_GET['genere'];

// if (isset($idGenere)) {
//   $connessione = new Service();
//   $a = $connessione->openConnection();
//   $aux = $connessione->insert_book(9788817108331, "Io sono il calcio", 3030, 300, 12.5, 10, "2018-10-03", "");

//   print_r($aux);

//   $connessione->closeConnection();
// } else {
// }
