<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    $paginaHTML = graphics::getPage("lasciaRecensione_php.html");

    // Accesso al database

    if (isset($_GET['isbn'])) {
        $isbn = $_GET['isbn'];

        $_SESSION["isbnReview"] = $isbn;

        // replace della breadcrumb
        $linkDettaglioLibro = "<a href='libro.php?isbn=" . $isbn . "'>Dettagli Libro</a>";
        $paginaHTML = str_replace("</linkDettaglioLibro>", $linkDettaglioLibro, $paginaHTML);
        // ------------------------

        $connessione = new Service();
        $a = $connessione->openConnection();

        $queryIsbn = $connessione->get_book_by_isbn($isbn);

        if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
            $tmp = $queryIsbn->get_result();

            // il libro c'è, ora controllo se c'è già una recensione (caso nel quale si voglia modificarla)

        } else {
            $_SESSION["error"] = "Hai già recensito questo libro.";
        }

        $connessione->closeConnection();
    } else {
        $_SESSION["info"] = "ISBN inserito non valido.";
    }
    // -------------------

    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert info'><i class='fa fa-close'  aria-hidden='true' aria-hidden='true'></i> " . $_SESSION["error"] . "</span>", $paginaHTML);
        unset($_SESSION["error"]);
    } else if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> " . $_SESSION["info"] . "</span>", $paginaHTML);
        unset($_SESSION["info"]);
    } else if (isset($_SESSION["success"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert success'><i class='fa fa-check' aria-hidden='true'></i> " . $_SESSION["success"] . "</span>", $paginaHTML);
        unset($_SESSION["success"]);
    } else {
        $paginaHTML = str_replace("</alert>", "", $paginaHTML);
    }

    echo $paginaHTML;
}
