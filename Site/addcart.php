<?php
if (!isset($_SESSION)) {
  session_start();
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

if (isset($_POST["aggiungiCarrello"])) {

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
      $c = cart::build_cart_from_session();
    }
    $c->add($isbn, $quant, $prezzo);
    $c->save();

    $_SESSION['success'] = "Libro aggiunto al carrello";
    header("Location: libro.php?isbn=" . $isbn);
  } else {
    header("Location: accedi.php");
  }
} else if (isset($_POST["aggiungiWhish"])) {
  if (isset($_SESSION["Nome"])) {
    $isbn = $_SESSION["isbncart"];
    $user = $_SESSION["Codice_identificativo"];

    if (isset($user) && isset($isbn)) {
      $c = new Service();
      $c->openConnection();
      $c->insert_into_wishlist($user, $isbn);
      $c->closeConnection();

      $_SESSION['success'] = "Libro aggiunto alla wishlist";
      header("Location: libro.php?isbn=" . $isbn);
    } else {
      header("Location: error.php");
    }
  } else {
    header("Location: accedi.php");
  }
}
