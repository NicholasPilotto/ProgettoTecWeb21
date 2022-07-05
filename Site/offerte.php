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
            $listaOfferte .= "<li><a href='libro.php?isbn=" . $libro['isbn'] . "'><img class='generiCardsImg' src='" . $libro['percorso'] . "' alt=''>" . $libro['titolo'] .  "</a> " . number_format((float)$libro['prezzo'] * (100 - $libro['sconto']) / 100, 2, '.', '') . "&euro; (" . $libro['sconto'] . "% sconto)" .  "</li>";
        }
    }
    $listaOfferte .= "</ul>";

    $paginaHTML = str_replace("</listaOfferte>", $listaOfferte, $paginaHTML);
}


$connessione->closeConnection();
// -------------------
$paginaHTML = str_replace('<li class="nav-item"><a href="offerte.php">Offerte</a></li>', '<li class="nav-item selectedNavItem">Offerte</li>', $paginaHTML);

echo $paginaHTML;
