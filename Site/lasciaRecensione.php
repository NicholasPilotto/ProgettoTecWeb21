<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

if (!isset($_SESSION["Nome"]) || !isset($_SESSION["Codice_identificativo"])) {
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

        if ($queryIsbn->ok() && !$queryIsbn->is_empty())
        {
            // il libro c'è, ora controllo se c'è già una recensione
            $queryControlloRecensione = $connessione->get_reviews_by_isbn($isbn);
            if(!$queryControlloRecensione->is_empty())
            {
                foreach($queryControlloRecensione->get_result() as $recensione)
                {
                    if($recensione['idUtente'] == $_SESSION["Codice_identificativo"])
                    {
                        $_SESSION["info"] = "Hai gi&agrave; recensito questo libro";
                        unset($_SESSION["paginaPrecedente"]);
                        header("Location: libro.php?isbn=" . $isbn);
                        die();
                    }
                }
            }
        }
        else
        {
            header("Location: error.php");
        }

        $connessione->closeConnection();
    } else {
        header("Location: error.php");
    }
    // -------------------

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
}
