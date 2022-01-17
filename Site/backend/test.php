<?php

use DB\Service;

require_once('db.php');

if ($_GET["f"] == 'a') {
  $connessione = new Service();
  $a = $connessione->openConnection();
  // $aux = $connessione->insert_book(9788817108331, "Io sono il calcio", 3030, 300, 12.5, 10, "2018-10-03", "");

  $aux = $connessione->signin("Mario", "Rossi", "1995-10-9", "mario", "mariorossi@gmail.com", "ciao", "3409281632");
  print_r(var_export($aux));
  $connessione->closeConnection();
}


$idGenere = $_GET['genere'];

if (isset($idGenere)) {
  $connessione = new Service();
  $a = $connessione->openConnection();
  $aux = $connessione->insert_book(9788817108331, "Io sono il calcio", 3030, 300, 12.5, 10, "2018-10-03", "");

  print_r($aux);

  $connessione->closeConnection();
} else {
}
