<?php

session_start();

if (!isset($_SESSION["Nome"])) {
    header("Location:index.php");
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$connessione = new Service();
$a = $connessione->openConnection();
$queryUtente = $connessione->get_utente_by_email($_SESSION["Email"]);

if (isset($_SESSION["cart"])) {
    $cart = new cart();
    $cart = cart::build_cart_from_session();

    $indirizzo = $_POST["indirizzo"];


    $data = $connessione->insert_order($queryUtente->get_result()[0]["codice_identificativo"], $indirizzo, $cart);

    if ($data->ok()) {
        if ($data->get_errno() == 0) {
            $_SESSION["success"] = "Ordine completato con successo.";
            $connessione->closeConnection();
            unset($_SESSION["cart"]);
            header("Location: carrello.php");
        } else {
            $_SESSION["info"] = $data->get_error_message();
            $connessione->closeConnection();
            header("Location: carrello.php");
        }
    } else {
        $_SESSION["error"] = "Non è stato possibile connettersi al sistema.";
        $connessione->closeConnection();
        header("Location: carrello.php");
    }
}
