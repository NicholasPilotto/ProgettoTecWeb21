<?php

use DB\Service;

require_once('db.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_GET["f"] == 'a') {
  $connessione = new Service();
  $a = $connessione->openConnection();
  // $aux = $connessione->insert_book(9788817108331, "Io sono il calcio", 3030, 300, 12.5, 10, "2018-10-03", "");

  // $aux = $connessione->get_addresses(1000000000);
  // $aux = $connessione->get_avg_review(9788822760265);
  $aux = $connessione->is_code_correct('c6dc9113ab65fe1b84814815028957d8', 1000000000);
  // var_dump(md5(uniqid(rand(), true)));
  print_r($aux->get_result());
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
