<?php

use DB\Service;

//path /Site/test.php?f=a

require_once('db.php');
if ($_GET['f'] == 'a') {
  $connessione = new Service();
  $a = $connessione->openConnection();
  $aux = $connessione->get_books_by_author('Elen', 'Bonelli');
  print_r(json_encode($aux));
  $connessione->closeConnection();
}
