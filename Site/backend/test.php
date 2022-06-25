<?php

use DB\Service;

require_once('../cart.php');
require_once('db.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if ($_GET["f"] == 'a') {
$connessione = new Service();
$a = $connessione->openConnection();
//   // $aux = $connessione->insert_book(9788817108331, "Io sono il calcio", 3030, 300, 12.5, 10, "2018-10-03", "");

//   // $aux = $connessione->get_addresses(1000000000);
//   // $aux = $connessione->get_avg_review(9788822760265);
//   $aux = $connessione->is_code_correct('c6dc9113ab65fe1b84814815028957d8', 1000000000);
//   // var_dump(md5(uniqid(rand(), true)));
//   print_r($aux->get_result());
//   $connessione->closeConnection();
// }

// $new = array("mail" => "a@b.c", "password" => "qwerty", "tel" => "1234567890");
// $old = array("mail" => "a@b.c", "password" => "qwerty", "tel" => "0987654321");

// $result = array_diff($new, $old);
// print_r($result);

// $aux = $connessione->insert_into_wishlist("1000000014", "9788820362713");
// $aux = $connessione->get_wishlist("1000000014");
// $aux = $connessione->remove_from_wishlist("1000000014", "9788830901988");

// $c = new cart();

// $c->add("9788828763604", 10, 3.00);

// $aux = $connessione->insert_order("1000000014", "100011", $c);

$isbn = isset($_GET["isbn"]) ? $_GET["isbn"] : NULL;
$user = isset($_GET["user"]) ? $_GET["user"] : NULL;

echo $user . " " . $isbn . "</br>";

$aux = $connessione->insert_into_wishlist($user, $isbn);


print_r($aux);

$connessione->closeConnection();

// print_r($c->get_cart());
// $c->remove("123456", 2);
// echo "<br /> <br />";
// print_r($c->get_cart());
// echo "<br /> <br />";
// echo $c->get_quantity() . "-" . $c->get_total();
// if ($_GET["f"] == 'a') {
//   echo "ciao";
// }

// $idGenere = $_GET['genere'];

// if (isset($idGenere)) {
//   $connessione = new Service();
//   $a = $connessione->openConnection();
//   $aux = $connessione->insert_book(9788817108331, "Io sono il calcio", 3030, 300, 12.5, 10, "2018-10-03", "");

//   print_r($aux);

// } else {
// }
