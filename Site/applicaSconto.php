<?php

session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
}

$paginaHTML = graphics::getPage("applicaSconto_php.html");

$codiceIdentificativo = $_SESSION["Codice_identificativo"];
$codiceIdentificativo = hash('sha256', $codiceIdentificativo);
$liAccount = "";

if ($codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
    // admin
    $connessione = new Service();
    $a = $connessione->openConnection();

    $errore = false;
    if (isset($_GET['isbn'])) {
        $isbn = $_GET['isbn'];
        $queryIsbn = $connessione->get_book_by_isbn($isbn);

        if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
            // replace della breadcrumb
            $linkDettaglioLibro = "<a href='libro.php?isbn=" . $isbn . "'>Dettagli Libro</a>";
            $paginaHTML = str_replace("</linkDettaglioLibro>", $linkDettaglioLibro, $paginaHTML);
            // ------------------------

            $libro = $queryIsbn->get_result()[0];
            $titolo = $libro['titolo'];

            $paginaHTML = str_replace("</titoloApplicaSconto>", $titolo, $paginaHTML);
            $paginaHTML = str_replace("</isbnApplicaSconto>", $isbn, $paginaHTML);

            $today = date('Y-m-d');
            $month = date('Y-m-d', strtotime('+ 30 days'));
            $paginaHTML = str_replace("</startDate>", $today, $paginaHTML);
            $paginaHTML = str_replace("</endDate>", $month, $paginaHTML);
        } else {
            $_SESSION["info"] = $queryIsbn->get_error_message();
        }
    } else {
        $_SESSION["info"] = "Nessun ISBN trovato";
    }
} else {
    // user normale
    header("Location: index.php");
}
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

echo $paginaHTML;
