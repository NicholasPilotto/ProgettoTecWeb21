<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("wishlist_php.html");

if(!isset($_SESSION["Nome"])){
    header("Location:accedi.php");
}

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();
$wishlistDiv = "";
$utente = $_SESSION["Codice_identificativo"];

$queryWishlist = $connessione->get_wishlist($utente);

foreach($queryWishlist->get_result() as $wish)
{
    $queryIsbn = $connessione->get_book_by_isbn($wish["libro_isbn"]);
    if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
        $res = $queryIsbn->get_result();
        $imgLibro = "<li class='libroCarrello'><img class='carrelloImg' alt='' src='" . $res[0]['percorso'] . "'></li>";
        $titolo = "<li class='liInfo'><p class='libroTitolo'>" . $res[0]['titolo'] . "&nbsp;</p>";
        $cost = "<p>Prezzo:" . $res[0]['prezzo'] . "</p>";
        $button = "<button type='button' class='cartButton' onclick='window.location.href=\"removewish.php?isbn=" . $res[0]['isbn'] . "\"'>Rimuovi</button>";
        $wishlistDiv .= "<ul class='cardDettagli'>" . $imgLibro . $titolo . $cost . $button . "</ul></li>";
    }
}

$connessione->closeConnection();
// -------------------
$paginaHTML = str_replace("</wishlist>", $wishlistDiv, $paginaHTML);
echo $paginaHTML;

?>
