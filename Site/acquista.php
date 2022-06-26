<?php


session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$paginaHTML = graphics::getPage("acquista_php.html");

$connessione = new Service();
$a = $connessione->openConnection();

if (!isset($_SESSION["Nome"])) {
    header("Location:accedi.php");
}

// Accesso al database
$carrelloDiv = "";

if (isset($_SESSION["cart"])) {
    $c = cart::build_cart_from_session();
    $tot = "<p class='carrelloStatus'>Costo totale ordine: &euro;" . $c->get_total() . "</p>";
    $paginaHTML = str_replace("</totale>", $tot, $paginaHTML);
    $selectIndirizzi = "";

    $queryAddresses = $connessione->get_addresses($_SESSION["Codice_identificativo"]);
    if ($queryAddresses->ok() && !$queryAddresses->is_empty()) {
        $selectIndirizzi = "<select class='styleSelect' id='indirizzo' name='indirizzo' required>";
        foreach ($queryAddresses->get_result() as $indirizzo) {
            $selectIndirizzi .= "<option value='" . $indirizzo['codice'] . "'>" . $indirizzo["via"] . ", " . $indirizzo["città"] . ", " . $indirizzo["num_civico"] . "</option>";
        }
        $selectIndirizzi .= "</select>";
    } else {
        $selectIndirizzi = "<select class='styleSelect' id='indirizzo' name='indirizzo' disabled><option></option></select>";
    }

    $paginaHTML = str_replace("</selectIndirizzi>", $selectIndirizzi, $paginaHTML);
} else {
    header("Location:carrello.php");
}

// -------------------
echo $paginaHTML;
