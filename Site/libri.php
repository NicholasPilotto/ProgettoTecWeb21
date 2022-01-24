<?php

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("libri_php.html");

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();

$queryLibri = $connessione->get_all_books();

if ($queryLibri->ok()) {
    $listaLibri = "<ul class='bookCards'>";
    $cont = 0;
    foreach ($queryLibri->get_result() as $libro) {
        $listaLibri .= "<li><a href='libro.php?isbn=" . $libro['ISBN'] . "'><img class='homeCardsImg' src='" . $libro['Percorso'] . "' alt=''>" . $libro['Titolo'] . "</a></li>";
    }
    $listaLibri .= "</ul>";

    $paginaHTML = str_replace("</listaLibri>", $listaLibri, $paginaHTML);
} else {
    // la query ha prodotto un errore
}


$connessione->closeConnection();
// -------------------

echo $paginaHTML;
