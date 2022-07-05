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

    $paginaPrecedenteModificaLibro = " &gt;&gt; <a lang='en' href='account.php'>Account</a> &gt;&gt; Aggiungi Libro"; // caso dall'account
    $titoloPagina = "Aggiungi Libro";
    if (isset($_SESSION["paginaPrecedenteModificaLibro"]))
    {
        $paginaPrecedenteModificaLibro = $_SESSION["paginaPrecedenteModificaLibro"];
        $paginaPrecedenteModificaLibro .= " &gt;&gt; Modifica Libro";
        $titoloPagina = "Modifica Libro";
    }

    $paginaHTML = graphics::getPage("aggiungiLibro_php.html");

    // replace della breadcrumb
    $paginaHTML = str_replace("</paginaPrecedenteModificaLibro>", $paginaPrecedenteModificaLibro, $paginaHTML);
    $paginaHTML = str_replace("</titoloPagina>", $titoloPagina, $paginaHTML);

    $codiceIdentificativo = $_SESSION["Codice_identificativo"];
    $codiceIdentificativo = hash('sha256', $codiceIdentificativo);
    $liAccount = "";

    if ($codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
        // admin
        $connessione = new Service();
        $a = $connessione->openConnection();

        if ($a) {

            // controllo se l'admin viene da "aggiungi libro" o da "modifica libro"
            $modificaLibroISBN = "";
            $modificaLibroTitolo = "";
            $modificaLibroCopertina = "images/placeholderimg.jpg";
            $modificaLibroPagine = "";
            $modificaLibroPrezzo = "";
            $modificaLibroQuantita = "";
            $modificaLibroDataPubblicazione = '1971-01-01';
            $modificaLibroTrama = "";
            $modificaLibroAutori = array();
            $modificaLibroCategorie = array();
            $modificaLibroEditore = "";
            if (isset($_GET['isbn']))
            {
                $_SESSION["editFlag"] = true;
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
                        array_push($modificaLibroAutori, $autore["autore_id"]);
                    }
                    // categorie
                    $cat = $connessione->get_genres_from_isbn($isbn);
                    foreach ($cat->get_result() as $categoria) {
                        array_push($modificaLibroCategorie, $categoria['id_categoria']);
                    }

                    // editore
                    $modificaLibroEditore = $libro['editore'];
                } else {
                    $_SESSION["info"] = "Nessun libro trovato con tale ISBN";
                }
            }
            else
            {
                unset($_SESSION["editFlag"]);
            }
        }
        else
        {
            $_SESSION["error"] = "Impossibile connettersi al sistema";
        }
        // -------------------------------------------------------------------

        $queryAutori = $connessione->get_all_authors();
        $selectAutori = "<select class='styleMultipleSelect' id='autore' name='autore[]' multiple>";
        if ($queryAutori->ok()) {
            foreach (OrderWithoutTags($queryAutori->get_result(), "nome") as $autore) {
                // se sono nella modifica, devo cercare gli autori del libro e selezionarli
                $selected = (in_array($autore['id'], $modificaLibroAutori)) ? "selected" : "";

                $cognome = ($autore['cognome'] != "-") ? $autore['cognome'] : "";

                $nome = strip_tags($autore['nome']);
                $cognome = strip_tags($cognome);

                $selectAutori .= "<option value='" . $autore['id'] . "' " . $selected . ">" . $nome . " " . $cognome . "</option>";
            }
        } else {
            $_SESSION["info"] = "Nessun autore trovato";
        }
        $selectAutori .= "</select>";

        $queryCategorie = $connessione->get_all_genres();
        $selectCategorie = "<select class='styleMultipleSelect' id='categoria' name='categoria[]' multiple>";
        if ($queryCategorie->ok()) {
            foreach (OrderWithoutTags($queryCategorie->get_result(), "id_categoria") as $categoria) {
                // se sono nella modifica, devo cercare gli autori del libro e selezionarli
                $selected = (in_array($categoria['id_categoria'], $modificaLibroCategorie)) ? "selected" : "";

                $selectCategorie .= "<option value='" . $categoria['id_categoria'] . "' " . $selected . ">" . $categoria['nome'] . "</option>";
            }
        } else {
            $_SESSION["info"] = "Nessuna categoria trovata";
        }
        $selectCategorie .= "</select>";

        $queryEditori = $connessione->get_all_editors();
        $selectEditori = "<select class='styleSelect' id='editore' name='editore'>";
        if ($queryEditori->ok()) {
            foreach (OrderWithoutTags($queryEditori->get_result(), "nome") as $editore) {
                // se sono nella modifica, devo cercare l'editore del libro e selezionarlo
                $selected = ($editore['id'] == $modificaLibroEditore) ? "selected" : "";

                $selectEditori .= "<option value='" . $editore['id'] . "' " . $selected . ">" . $editore['nome'] . "</option>";
            }
        } else {
            $_SESSION["info"] = "Nessun editore trovato";
        }

        $selectEditori .= "</select>";
        $isbnSettings = "";
        if (isset($_SESSION["editFlag"])) {
            $isbnSettings = "readonly";
        }
        $paginaHTML = str_replace("</isbnSettings>", $isbnSettings, $paginaHTML);
        $paginaHTML = str_replace("</selectAutori>", $selectAutori, $paginaHTML);
        $paginaHTML = str_replace("</selectCategorie>", $selectCategorie, $paginaHTML);
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
    } else {
        // user normale
        header("Location: index.php");
    }
}
