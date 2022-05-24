<?php

session_start();

if(!isset($_SESSION["Nome"]))
{
    header("Location:index.php");
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$connessione = new Service();
$a = $connessione->openConnection();
$queryUtente = $connessione->get_utente_by_email($_SESSION["Email"]);

if(isset($_SESSION["cart"]))
{
    $cart = new cart();
    $cart = cart::build_cart_from_session();
    print_r($cart);
    echo "<br>";
    $indirizzo = $_POST["indirizzo"];
    echo $indirizzo;
    echo "<br>";
    echo $queryUtente->get_result()[0]["codice_identificativo"];
    echo "<br>";
    $queryInsert = $connessione->insert_order($queryUtente->get_result()[0]["codice_identificativo"],$indirizzo,$cart);
    print_r($queryInsert);
    //unset($_SESSION["cart"]);
}
$connessione->closeConnection();
//header("Location:index.php");
?>