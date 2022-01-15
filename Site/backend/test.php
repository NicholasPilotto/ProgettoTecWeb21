<?php

use DB\Service;

require_once('db.php');
if ($_GET['f'] == 'a') {
  $connessione = new Service();
  $a = $connessione->openConnection();
  $aux = $connessione->get_book_by_title('La canzone romana');
  print_r(json_encode($aux));
  // $connessione->closeConnection();


  // $a = new mysqli("127.0.0.1", "root", "", "tecweb");

  // if ($a->connect_error) {
  //   echo "Connection failed: " . $a->connect_error;
  // }

  // $r = $a->query("SELECT * FROM personaggi ORDER BY id ASC");

  // while ($row = $r->fetch_assoc()) {
  //   echo $row['nome'];
  // }

  // mysqli_close($a);
}
