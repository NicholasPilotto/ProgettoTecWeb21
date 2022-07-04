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
if (!$a) {
    $_SESSION["error"] = "Non è stato possibile connettersi al sistema.";
}
else
{
    if (isset($_SESSION["Email"])) {
        $queryUtente = $connessione->get_utente_by_email($_SESSION["Email"]);

        if (isset($_SESSION["cart"])) {
            $cart = new cart();
            $cart = cart::build_cart_from_session();

            $indirizzo = $_POST["indirizzo"];
            if (!empty($indirizzo) && !empty($_POST["nomecognome"]) && !empty($_POST["numCarta"]) && !empty($_POST["dataScadenza"]) && !empty($_POST["csv"]) && $queryUtente->ok())
            {
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
                    }
                } else {
                    $_SESSION["info"] = $data->get_error_message();
                    $connessione->closeConnection();
                }
            } else {
                $_SESSION["info"] = "Qualche dato mancante";
                $connessione->closeConnection();
            }
        }
    } else {
        $_SESSION["info"] = "Qualche dato mancante";
        $connessione->closeConnection();
    }
}
header("Location: acquista.php");
