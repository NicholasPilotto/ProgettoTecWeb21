<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$paginaHTML = graphics::getPage("carrello_php.html");

$codiceIdentificativo = $_SESSION["Codice_identificativo"];
$codiceIdentificativo = hash('sha256', $codiceIdentificativo);
if (!isset($_SESSION["Nome"]) || $codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
    header("Location:accedi.php");
}

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();
$carrelloDiv = "";

if (isset($_SESSION["cart"])) {
    $c = new cart();
    $c = cart::build_cart_from_session();
    $cart = $c->get_cart();
    $tot = 0;

    foreach ($cart as $isbn => $data) {
        $queryIsbn = $connessione->get_book_by_isbn($isbn);
        if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
            $res = $queryIsbn->get_result();
            $imgLibro = "<li class='libroCarrello'><img class='carrelloImg' alt='' src='" . $res[0]['percorso'] . "'></li>";
            $titolo = "<li class='liInfo'><p class='libroTitolo'>" . $res[0]['titolo'] . "&nbsp;</p>";
            $qt = "<p>Quantit&agrave;: " . $data->quant . "</p>";
            $cost = "<p>Costo totale: &euro;" . $data->total . "</p>";
            //$button = "<button type='button' class='button cartButton' onclick='window.location.href=\"removecart.php?isbn=" . $isbn . "\"'>Rimuovi</button>";
            $button = "<form method='post' action='removecart.php?isbn=" . $isbn . "'><input class='button cartButton' type='submit' value='Rimuovi'/></form>";
            $carrelloDiv .= "<ul class='cardDettagli'>" . $imgLibro . $titolo . $qt . $cost . $button . "</ul></li>";
            $tot += $data->total;
        }
    }

    $purchase = "<form method='post' action='acquista.php'><input type='submit' class='button procediAcquistoButton' value='Procedi con l&lsquo;acquisto'/></form>";
    //$purchase = "<button type='button' class='button procediAcquistoButton' onclick='window.location.href=\"acquista.php\"'>Procedi con l'acquisto</button>";
    $totString = "<div class='carrelloStatus'><p>" . "Prezzo totale: &euro;" . $tot . "</p>" . $purchase . "</div>";
    $paginaHTML = str_replace("</totale>", $totString, $paginaHTML);
} else {
    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</totale>", "<span class='alert error'><i class='fa fa-times'  aria-hidden='true'></i> " . $_SESSION["error"] . "</span></br>", $paginaHTML);
        unset($_SESSION["error"]);
    } else if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</totale>", "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> " . $_SESSION["info"] . "</span></br>", $paginaHTML);
        unset($_SESSION["info"]);
    } else if (isset($_SESSION["success"])) {
        $paginaHTML = str_replace("</totale>", "<span class='alert success'><i class='fa fa-check' aria-hidden='true'></i> " . $_SESSION["success"] . "</span></br>", $paginaHTML);
        unset($_SESSION["success"]);
    } else {
        // $paginaHTML = str_replace("</alert>", "", $paginaHTML);
        $carrelloDiv .= "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Il carrello è vuoto</span></br>";
    }
    $paginaHTML = str_replace("</totale>", "", $paginaHTML);
}

$connessione->closeConnection();
// -------------------
$paginaHTML = str_replace("</carrello>", $carrelloDiv, $paginaHTML);
$paginaHTML = str_replace('<abbr class="notification"', '<abbr class="notification selectedNavItem"', $paginaHTML);
$paginaHTML = str_replace('<a href="carrello.php">', '', $paginaHTML);
$paginaHTML = str_replace('</a">', '', $paginaHTML);
echo $paginaHTML;
