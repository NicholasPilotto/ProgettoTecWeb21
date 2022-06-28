<?php

session_start();

use DB\Service;

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    require_once('backend/db.php');
    require_once "graphics.php";

    $paginaHTML = graphics::getPage("applicaSconto_php.html");

    $codiceIdentificativo = $_SESSION["Codice_identificativo"];
    $codiceIdentificativo = hash('sha256', $codiceIdentificativo);
    $liAccount = "";

    if ($codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
        // admin
        $connessione = new Service();
        $a = $connessione->openConnection();

        $errore = false;
        if (isset($_GET['isbn']))
        {
            $isbn = $_GET['isbn'];
            $queryIsbn = $connessione->get_book_by_isbn($isbn);

            if ($queryIsbn->ok() && !$queryIsbn->is_empty())
            {
                // replace della breadcrumb
                $linkDettaglioLibro = "<a href='libro.php?isbn=" . $isbn . "'>Dettagli Libro</a>";
                $paginaHTML = str_replace("</linkDettaglioLibro>", $linkDettaglioLibro, $paginaHTML);
                // ------------------------

                $libro = $queryIsbn->get_result()[0];
                $titolo = $libro['titolo'];

                $paginaHTML = str_replace("</titoloApplicaSconto>", $titolo, $paginaHTML);
                $paginaHTML = str_replace("</isbnApplicaSconto>", $isbn, $paginaHTML);
            }
            else
            {
                $errore = true;
            }
        }
        else
        {
            $errore = true;
        }

        if($errore)
        {
            // errore
        }
        // -------------------------------------------------------------------

        echo $paginaHTML;
    }
    else
    {
        // user normale
        header("Location: index.php");
    }
}
