<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$isbn = isset($_SESSION["isbncart"]) ? $_SESSION["isbncart"] : NULL;
$quant = isset($_POST["quantita"]) ? $_POST["quantita"] : NULL;
$prezzo = isset($_SESSION["pricecart"]) ? $_SESSION["pricecart"] : NULL;

unset($_SESSION["isbncart"]);
unset($_SESSION["pricecart"]);

if (isset($_SESSION["Nome"])) {
  $c;
  if (!isset($_SESSION["cart"])) {
    $c = new cart();
  } else {
    $c = unserialize($_SESSION["cart"]);
  }

  $c->add($isbn, $quant, $prezzo);

  $_SESSION["cart"] = serialize($c);
} else {
  header("Location: accedi.php");
}
