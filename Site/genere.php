<?php

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("genere_php.html");

// Accesso al database

$trovatoErrore = false;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['genere'])) {
    $idGenere = $_GET['genere'];

    $connessione = new Service();
    $a = $connessione->openConnection();

    $queryNomeGenere = $connessione->get_genre_by_id($idGenere);
    if ($queryNomeGenere->ok()) {

        if ($queryNomeGenere->get_element_count() > 0) {
            // Ce un genere con quell'id, posso andare avanti
            $nomeGenere = $queryNomeGenere->get_result()[0]['Nome'];
            $libri = $connessione->get_books_by_genre($idGenere);

            if ($libri->ok()) {


                $listaLibri = "<ul class='bookCards'>";

                foreach ($libri->get_result() as $libro) {
                    $listaLibri .= "<li><a href='libro.php?isbn=" . $libro['ISBN'] . "'><img class='generiCardsImg' src='" . $libro['Percorso'] . "' alt=''>" . $libro['Titolo'] . "</a></li>";
                }

                $listaLibri .= "</ul>";

                $paginaHTML = str_replace("</listaLibri>", $listaLibri, $paginaHTML);
                $paginaHTML = str_replace("</nomeGenere>", $nomeGenere, $paginaHTML);
            } else {
                $listaLibri .= "<ul>" . $libri->get_element_count() . "</ul>";
                $paginaHTML = str_replace("</listaLibri>", $listaLibri, $paginaHTML);
                $paginaHTML = str_replace("</nomeGenere>", $nomeGenere, $paginaHTML);
            }
        } else {
            $trovatoErrore = true;
        }
    } else {
    }

    $connessione->closeConnection();
} else {
    $trovatoErrore = true;
}

if ($trovatoErrore) {
    // Errore, pagina senza genereId o con idGenere sbagliato
    $errore = "<img src='images/404.jpg' alt='Errore 404, genere inesistente' id='erroreImg'>";

    $paginaHTML = str_replace("</listaLibri>", $errore, $paginaHTML);
    $paginaHTML = str_replace("</nomeGenere>", "Errore", $paginaHTML);
}

// -------------------

echo $paginaHTML;
