<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$paginaHTML = graphics::getPage("carrello_php.html");

// setto sessione per paginaPrecedente, che era stata cancellata in getPage()
$url = explode("/", $_SERVER['REQUEST_URI']);
$current = end($url);

$_SESSION['paginaPrecedente'] = " &gt;&gt; <a href='" . $current . "'>Carrello</a>";
// -------------------------------------------------------------------------

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

    $carrelloDiv = "<ul class='carrelloCards'>";
    foreach ($cart as $isbn => $data)
    {
        $queryIsbn = $connessione->get_book_by_isbn($isbn);
        if ($queryIsbn->ok() && !$queryIsbn->is_empty())
        {
            $res = $queryIsbn->get_result();

            $carrelloDiv .= "<li>";
            $carrelloDiv .= "<a href='libro.php?isbn=" . $isbn . "'><img class='carrelloImg' src='" . $res[0]['percorso'] . "' alt=''></a>";
            $carrelloDiv .= "<div>";
            $carrelloDiv .= "<a class='titolo' href='libro.php?isbn=" . $isbn . "'>" . $res[0]['titolo'] . "</a>";
            $carrelloDiv .= "<p><span class='miniGrassetto'>Quantit&agrave;:</span> " . $data->quant . "</p>";
            $carrelloDiv .= "<p><span class='miniGrassetto'>Costo totale:</span> &euro;" . number_format((float)$data->total, 2, '.', '') . "</p>";
            $carrelloDiv .= "<form method='post' id='removeCartForm' action='removecart.php?isbn=" . $isbn . "'><input class='button cartButton' type='submit' value='Rimuovi'/></form>";
            $carrelloDiv .= "</div>";
            $carrelloDiv .= "</li>";

            $tot += $data->total;
        }
    }
    $carrelloDiv .= "</ul>";

    $purchase = "<form class='formOrdine' method='post' action='acquista.php'><input type='submit' class='button procediAcquistoButton' value='Procedi con l&lsquo;acquisto'/></form>";
    $totString = "<p class='carrelloStatus'>" . "<span class='boldText'>Prezzo totale:</span> &euro;" . number_format((float)$tot, 2, '.', '') . "</p>" . $purchase;
    $paginaHTML = str_replace("</totale>", $totString, $paginaHTML);
} else {
    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</totale>", graphics::createAlert("error", $_SESSION["error"]), $paginaHTML);
        unset($_SESSION["error"]);
    }
    if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</totale>", graphics::createAlert("info", $_SESSION["info"]), $paginaHTML);
        unset($_SESSION["info"]);
    }
    if (isset($_SESSION["success"])) {
        $paginaHTML = str_replace("</totale>", graphics::createAlert("success", $_SESSION["success"]), $paginaHTML);
        unset($_SESSION["success"]);
    }
    else
    {
        $carrelloDiv .= graphics::createAlert("info", "Il carrello &egrave; vuoto");
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
