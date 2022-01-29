<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("offerte_php.html");

// setto sessione per paginaPrecedente, che era stata cancellata in getPage()
$_SESSION['paginaPrecedente'] = " &gt;&gt; <a href='offerte.php'>Offerte</a>";
// -------------------------------------------------------------------------

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();

$limit = 12;

$queryOfferte = $connessione->get_books_with_offers();

if ($queryOfferte->ok()) {

    $listaOfferte = "<ul class='bookCards'>";
    $cont = 0;
    foreach ($queryOfferte->get_result() as $libro) {
        if ($cont++ < $limit) {
            $listaOfferte .= "<li><a href='libro.php?isbn=" . $libro['ISBN'] . "'><img class='generiCardsImg' src='" . $libro['Percorso'] . "' alt=''>" . $libro['Titolo'] .  "</a></br>" . number_format((float)$libro['Prezzo'] * (100-$libro['Sconto'])/100, 2, '.', '') . "&euro; (" . $libro['Sconto'] . "% sconto)" .  "</li>";
        }
    }
    $listaOfferte .= "</ul>";

    $paginaHTML = str_replace("</listaOfferte>", $listaOfferte, $paginaHTML);
}


$connessione->closeConnection();
// -------------------

echo $paginaHTML;
