<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once("graphics.php");

$paginaHTML = graphics::getPage("genere_php.html");

// la sessione per paginaPrecedente la setto più sotto perché mi serve il nome del genere

// Accesso al database

$trovatoErrore = false;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['genere'])) {
    $idGenere = $_GET['genere'];

    $connessione = new Service();
    $a = $connessione->openConnection();

    if($a)
    {
        $queryNomeGenere = $connessione->get_genre_by_id($idGenere);
        if ($queryNomeGenere->ok() && !$queryNomeGenere->is_empty()) {
            // Ce un genere con quell'id, posso andare avanti
            $nomeGenere = $queryNomeGenere->get_result()[0]['nome'];
            $libri = $connessione->get_books_by_genre($idGenere);

            if ($libri->ok()) {
                $listaLibri = "<ul class='bookCards'>";

                foreach ($libri->get_result() as $libro) {
                    $listaLibri .= "<li><a href='libro.php?isbn=" . $libro['isbn'] . "'><img class='generiCardsImg' src='" . $libro['percorso'] . "' alt=''>" . $libro['titolo'] . "</a></li>";
                }

                $listaLibri .= "</ul>";

                $paginaHTML = str_replace("</listaLibri>", $listaLibri, $paginaHTML);
                $paginaHTML = str_replace("</nomeGenere>", $nomeGenere, $paginaHTML);

                // setto sessione per paginaPrecedente, che era stata cancellata in getPage()
                $_SESSION['paginaPrecedente'] = " &gt;&gt; <a href='generi.php'>Generi</a> &gt;&gt; <a href='genere.php?genere=" . $idGenere . "'>" . $nomeGenere . "</a>";
                // -------------------------------------------------------------------------
            }
            else
            {
                $_SESSION["info"] = "Nessun libro trovato";
            }
        } else {
            $trovatoErrore = true;
        }

        $connessione->closeConnection();
    }
    else
    {
        $_SESSION["error"] = "Impossibile connettersi al sistema";
    }
}
else
{
    $trovatoErrore = true;
}

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
} else {
    $paginaHTML = str_replace("</alert>", "", $paginaHTML);
}

if ($trovatoErrore) {
    header("Location: error.php");
}

// -------------------

echo $paginaHTML;
