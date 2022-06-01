<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$paginaHTML = graphics::getPage("carrello_php.html");

if(!isset($_SESSION["Nome"])){
    header("Location:accedi.php");
}

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();
$carrelloDiv = "";

if(isset($_SESSION["cart"]))
{
    $c = new cart();
    $c = cart::build_cart_from_session();
    $cart = $c->get_cart();
    $tot = 0;

    foreach($cart as $isbn => $data)
    {
        $queryIsbn = $connessione->get_book_by_isbn($isbn);
        if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
            $res = $queryIsbn->get_result();
            $imgLibro = "<li class='libroCarrello'><img class='carrelloImg' alt='' src='" . $res[0]['percorso'] . "'></li>";
            $titolo = "<li class='liInfo'><p class='libroTitolo'>" . $res[0]['titolo'] . "&nbsp;</p>";
            $qt = "<p>Quantità:" . $data->quant . "</p>";
            $cost = "<p>Costo totale:" . $data->total . "</p>";
            $button = "<button type='button' class='cartButton' onclick='window.location.href=\"removecart.php?isbn=" . $isbn . "\"'>Rimuovi</button>";
            $carrelloDiv .= "<ul class='cardDettagli'>" . $imgLibro . $titolo . $qt . $cost . $button . "</ul></li>";
            $tot += $data->total;
        }
    }

    $purchase = "<button type='button' class='cartButton' onclick='window.location.href=\"acquista.php\"'>Procedi con l'acquisto</button>";
    $totString = "<div class='carrelloStatus'>" . $purchase . "<p>" . "Prezzo totale:" . $tot . "</p></div>";
    $paginaHTML = str_replace("</totale>", $totString, $paginaHTML);
}
else
{
    $carrelloDiv .= "<div class='carrelloStatus'><p>Il carrello è vuoto</p></div>";
    $paginaHTML = str_replace("</totale>", "", $paginaHTML);
}

$connessione->closeConnection();
// -------------------
$paginaHTML = str_replace("</carrello>", $carrelloDiv, $paginaHTML);
echo $paginaHTML;

?>