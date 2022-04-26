<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("libri_php.html");

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();

$queryLibri = $connessione->get_all_books();

if ($queryLibri->ok())
{
    $listaLibri = "<ul class='bookCards'>";
    $cont = 0;
    $libri = array();
    foreach ($queryLibri->get_result() as $libro)
    {
        if(!in_array($libro['isbn'], $libri))
        {
            array_push($libri, $libro['isbn']);
            $listaLibri .= "<li><a href='libro.php?isbn=" . $libro['isbn'] . "'><img class='homeCardsImg' src='" . $libro['percorso'] . "' alt=''>" . $libro['titolo'] . "</a></li>";
        }
    }
    $listaLibri .= "</ul>";

    $paginaHTML = str_replace("</listaLibri>", $listaLibri, $paginaHTML);
} else {
    // la query ha prodotto un errore
}


$connessione->closeConnection();
// -------------------

echo $paginaHTML;
