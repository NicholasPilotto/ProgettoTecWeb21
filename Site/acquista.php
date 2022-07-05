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
    $tot = "<p class='totaleAcquisto'><span class='miniGrassetto'>Costo totale ordine:</span> &euro;" . $c->get_total() . "</p>";
    $selectIndirizzi = "";

    $scadenza = "1971-01-01";

    $queryAddresses = $connessione->get_addresses($_SESSION["Codice_identificativo"]);
    if ($queryAddresses->ok() && !$queryAddresses->is_empty()) {
        $selectIndirizzi = "<select class='styleSelect' id='indirizzo' name='indirizzo'>";
        foreach ($queryAddresses->get_result() as $indirizzo) {
            $selectIndirizzi .= "<option value='" . $indirizzo['codice'] . "'>" . $indirizzo["via"] . ", " . $indirizzo["città"] . ", " . $indirizzo["num_civico"] . "</option>";
        }
        $selectIndirizzi .= "</select>";
    } else {
        $selectIndirizzi = "<span><a class='indirizziError' id='indirizzo' name='indirizzo' href='aggiungiIndirizzo.php'>Devi prima salvare un indirizzo</a></span>";
    }

    $paginaHTML = str_replace("</totale>", $tot, $paginaHTML);
    $paginaHTML = str_replace("</scadenza>", $scadenza, $paginaHTML);
    $paginaHTML = str_replace("</selectIndirizzi>", $selectIndirizzi, $paginaHTML);

    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("error", $_SESSION["error"]), $paginaHTML);
        unset($_SESSION["error"]);
    }
    if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("info", $_SESSION["info"]), $paginaHTML);
        unset($_SESSION["info"]);
    }
    if (isset($_SESSION["success"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("success", $_SESSION["success"]), $paginaHTML);
        unset($_SESSION["success"]);
    } else {
        $paginaHTML = str_replace("</alert>", "", $paginaHTML);
    }
} else {
    header("Location:carrello.php");
}

// -------------------
echo $paginaHTML;
