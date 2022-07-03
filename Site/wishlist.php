<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("wishlist_php.html");

if (!isset($_SESSION["Nome"])) {
    header("Location:accedi.php");
}

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();
$wishlistDiv = "";
$utente = $_SESSION["Codice_identificativo"];

$queryWishlist = $connessione->get_wishlist($utente);

foreach ($queryWishlist->get_result() as $wish) {
    $queryIsbn = $connessione->get_book_by_isbn($wish["libro_isbn"]);
    if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
        $res = $queryIsbn->get_result();
        $imgLibro = "<li class='libroCarrello'><img class='carrelloImg' alt='' src='" . $res[0]['percorso'] . "'></li>";
        $titolo = "<li class='liInfo'><p class='libroTitolo'>" . $res[0]['titolo'] . "&nbsp;</p>";
        $cost = "<p>Prezzo: &euro;" . $res[0]['prezzo'] . "</p>";
        $button = "<form action='removewish.php'>
                    <input type='submit' class='button procediAcquistoButton' value='Rimuovi'</input>
                    <input type='hidden' name='wishtoremove' id='wishtoremove' value='" . $res[0]['isbn'] . "'/>
                    </form>";
        $wishlistDiv .= "<ul class='cardDettagli'>" . $imgLibro . $titolo . $cost . $button . "</ul></li>";
    }
}

$connessione->closeConnection();
// -------------------
if ($queryWishlist->get_element_count() != 0) {
    $paginaHTML = str_replace("</wishlist>", $wishlistDiv, $paginaHTML);
    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert error'><i class='fa fa-times'  aria-hidden='true'></i> " . $_SESSION["error"] . "</span>", $paginaHTML);
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
} else {
    $paginaHTML = str_replace("</wishlist>", "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> La wishlist è vuota</span></br>", $paginaHTML);
}


echo $paginaHTML;
