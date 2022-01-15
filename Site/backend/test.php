<?php

use DB\Service;
require_once('db.php');

$idGenere = $_GET['genere'];

if(isset($idGenere))
{
  $connessione = new Service();
  $a = $connessione->openConnection();
  $aux = $connessione->get_books_by_genre($idGenere);

  print_r($aux);

  $connessione->closeConnection();
}
else
{

}

?>