<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("wishlist_php.html");

// setto sessione per paginaPrecedente, che era stata cancellata in getPage()
$url = explode("/", $_SERVER['REQUEST_URI']);
$current = end($url);

$_SESSION['paginaPrecedente'] = " &gt;&gt; <a href='account.php'>Account</a> &gt;&gt; <a href='wishlist.php'>Wishlist</a>";
// -------------------------------------------------------------------------

if (!isset($_SESSION["Nome"])) {
    header("Location:accedi.php");
}

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();
$utente = $_SESSION["Codice_identificativo"];
$queryWishlist = $connessione->get_wishlist($utente);

$wishlistDiv = "<ul class='carrelloCards'>";
foreach ($queryWishlist->get_result() as $wish) {
    $queryIsbn = $connessione->get_book_by_isbn($wish["libro_isbn"]);
    if ($queryIsbn->ok() && !$queryIsbn->is_empty())
    {
        $res = $queryIsbn->get_result();

        // controllo offerta
        $offertaQuery = $connessione->get_active_offer_by_isbn($wish["libro_isbn"]);

        $textPrezzo = "<p><span class='miniGrassetto'>Prezzo:</span> &euro;" . $res[0]['prezzo'] . "</p>";

        if ($offertaQuery->ok())
        {
            $prezzo = number_format((float)$res[0]['prezzo'] * (100 - $offertaQuery->get_result()[0]['sconto']) / 100, 2, '.', '') . " (" . $offertaQuery->get_result()[0]['sconto'] . "% sconto)";
            $textPrezzo = "<p class='miniGrassetto'>Sconto da <del>&euro;" . $res[0]['prezzo'] . "</del> a &euro;" . $prezzo . "</p>";
        }
        // ---------

        $wishlistDiv .= "<li>";
        $wishlistDiv .= "<a href='libro.php?isbn=" . $res[0]['isbn'] . "'><img class='carrelloImg' src='" . $res[0]['percorso'] . "' alt=''></a>";
        $wishlistDiv .= "<div>";
        $wishlistDiv .= "<a class='titolo' href='libro.php?isbn=" . $res[0]['isbn'] . "'>" . $res[0]['titolo'] . "</a>";
        $wishlistDiv .= $textPrezzo;
        $wishlistDiv .= "<form action='removewish.php'>
                            <input type='submit' class='button procediAcquistoButton' value='Rimuovi'</input>
                            <input type='hidden' name='wishtoremove' id='wishtoremove' value='" . $res[0]['isbn'] . "'/>
                        </form>";
        $wishlistDiv .= "</div>";
        $wishlistDiv .= "</li>";
    }
}
$wishlistDiv .= "</ul>";

$connessione->closeConnection();
// -------------------
if ($queryWishlist->get_element_count() != 0) {
    $paginaHTML = str_replace("</wishlist>", $wishlistDiv, $paginaHTML);
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
    }
    else
    {
        $paginaHTML = str_replace("</alert>", "", $paginaHTML);
    }
} else {
    $paginaHTML = str_replace("</wishlist>", graphics::createAlert("info", "La wishlist &egrave vuota"), $paginaHTML);
}


echo $paginaHTML;
