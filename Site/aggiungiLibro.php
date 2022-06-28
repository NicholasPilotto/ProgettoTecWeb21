<?php

use DB\Service;

require_once('backend/db.php');
require_once 'graphics.php';

function OrderWithoutTags($array, $attr) {
    $newArray = array();
    foreach ($array as $elem) {
        $elem[$attr] = strip_tags($elem[$attr]);
        array_push($newArray, $elem);
    }
    $column = array_column($newArray, $attr);
    array_multisort($column, SORT_ASC, $newArray);
    return $newArray;
}

session_start();



if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    require_once('backend/db.php');
    require_once "graphics.php";

    $paginaHTML = graphics::getPage("aggiungiLibro_php.html");

    $codiceIdentificativo = $_SESSION["Codice_identificativo"];
    $codiceIdentificativo = hash('sha256', $codiceIdentificativo);
    $liAccount = "";

    if ($codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
        // admin
        $connessione = new Service();
        $a = $connessione->openConnection();

        // controllo se l'admin viene da "aggiungi libro" o da "modifica libro"
        $modificaLibroISBN = "";
        $modificaLibroTitolo = "";
        $modificaLibroCopertina = "";
        $modificaLibroPagine = "";
        $modificaLibroPrezzo = "";
        $modificaLibroQuantita = "";
        $modificaLibroDataPubblicazione = "";
        $modificaLibroTrama = "";
        $modificaLibroAutori = array();
        $modificaLibroEditore = "";
        if (isset($_GET['isbn'])) {
            // modifica libro
            $isbn = $_GET['isbn'];
            $queryIsbn = $connessione->get_book_by_isbn($isbn);

            if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
                $libro = $queryIsbn->get_result()[0];

                $modificaLibroISBN = $isbn;
                $modificaLibroTitolo = $libro['titolo'];
                $modificaLibroCopertina = $libro['percorso'];
                $modificaLibroPagine = $libro['pagine'];
                $modificaLibroPrezzo = $libro['prezzo'];
                $modificaLibroQuantita = $libro['quantita'];
                $modificaLibroDataPubblicazione = $libro['data_pubblicazione'];
                $modificaLibroTrama = $libro['trama'];
                // autori
                foreach ($queryIsbn->get_result() as $autore) {
                    array_push($modificaLibroAutori, $autore['autore_id']);
                }
                // editore
                $modificaLibroEditore = $libro['editore'];
            }
        }
        // -------------------------------------------------------------------

        $queryAutori = $connessione->get_all_authors();
        $selectAutori = "<select class='styleMultipleSelect' id='autore' name='autore[]' multiple>";
        if ($queryAutori->ok()) {
            foreach (OrderWithoutTags($queryAutori->get_result(), "nome") as $autore) {
                // se sono nella modifica, devo cercare gli autori del libro e selezionarli
                $selected = (in_array($autore['id'], $modificaLibroAutori)) ? "selected" : "";

                $cognome = ($autore['cognome'] != "-") ? $autore['cognome'] : "";
                $selectAutori .= "<option value='" . $autore['id'] . "' " . $selected . ">" . $autore['nome'] . " " . $cognome . "</option>";
            }
        }
        $selectAutori .= "</select>";

        $queryEditori = $connessione->get_all_editors();
        $selectEditori = "<select class='styleSelect' id='editore' name='editore' required>";
        if ($queryEditori->ok()) {
            foreach (OrderWithoutTags($queryEditori->get_result(), "nome") as $editore) {
                // se sono nella modifica, devo cercare l'editore del libro e selezionarlo
                $selected = ($editore['id'] == $modificaLibroEditore) ? "selected" : "";

                $selectEditori .= "<option value='" . $editore['id'] . "' " . $selected . ">" . $editore['nome'] . "</option>";
            }
        }
        $selectEditori .= "</select>";

        // replace
        $paginaHTML = str_replace("</selectAutori>", $selectAutori, $paginaHTML);
        $paginaHTML = str_replace("</selectEditori>", $selectEditori, $paginaHTML);
        // -------
        // replace dei campi input: se è in modifica le variabili hanno i valori, se è in aggiungi le variabili sono vuote
        $paginaHTML = str_replace("</modificaLibroISBN>", $modificaLibroISBN, $paginaHTML);
        $paginaHTML = str_replace("</modificaLibroTitolo>", $modificaLibroTitolo, $paginaHTML);
        $paginaHTML = str_replace("</modificaLibroCopertina>", $modificaLibroCopertina, $paginaHTML);
        $paginaHTML = str_replace("</modificaLibroPagine>", $modificaLibroPagine, $paginaHTML);
        $paginaHTML = str_replace("</modificaLibroPrezzo>", $modificaLibroPrezzo, $paginaHTML);
        $paginaHTML = str_replace("</modificaLibroQuantita>", $modificaLibroQuantita, $paginaHTML);
        $paginaHTML = str_replace("</modificaLibroDataPubblicazione>", $modificaLibroDataPubblicazione, $paginaHTML);
        $paginaHTML = str_replace("</modificaLibroTrama>", $modificaLibroTrama, $paginaHTML);

        if (isset($_SESSION["error"])) {
            $paginaHTML = str_replace("</alert>", "<span class='alert error'><i class='fa fa-close'></i> " . $_SESSION["error"] . "</span>", $paginaHTML);
            unset($_SESSION["error"]);
        } else if (isset($_SESSION["info"])) {
            $paginaHTML = str_replace("</alert>", "<span class='alert info'><i class='fa fa-exclamation-trinagle' aria-hidden='true'></i> " . $_SESSION["info"] . "</span>", $paginaHTML);
            unset($_SESSION["info"]);
        } else if (isset($_SESSION["success"])) {
            $paginaHTML = str_replace("</alert>", "<span class='alert success'><i class='fa fa-check' aria-hidden='true'></i> " . $_SESSION["success"] . "</span>", $paginaHTML);
            unset($_SESSION["success"]);
        } else {
            $paginaHTML = str_replace("</alert>", "", $paginaHTML);
        }

        echo $paginaHTML;
    } else {
        // user normale
        header("Location: index.php");
    }
}
