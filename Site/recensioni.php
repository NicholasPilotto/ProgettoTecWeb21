<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    $paginaHTML = graphics::getPage("recensioni_php.html");

    // setto sessione per paginaPrecedente, che era stata cancellata in getPage()
    $url = explode("/", $_SERVER['REQUEST_URI']);
    $current = end($url);

    $_SESSION['paginaPrecedente'] = " &gt;&gt; <a href='account.php'>Account</a> &gt;&gt; <a href='" . $current . "'>Recensioni</a>";
    // -------------------------------------------------------------------------

    // Accesso al database
    $connessione = new Service();
    $a = $connessione->openConnection();

    $queryRecensioni = $connessione->get_reviews_by_user($_SESSION["Codice_identificativo"]);

    $listaRecensioni = "";
    if ($queryRecensioni->ok() && !$queryRecensioni->is_empty()) {
        $listaRecensioni .= "<ul id='listaRecensioni'>";
        $cont = 0;
        $arrayMesi = array(
            "01" => "Gennaio",
            "02" => "Febbraio",
            "03" => "Marzo",
            "04" => "Aprile",
            "05" => "Maggio",
            "06" => "Giugno",
            "07" => "Luglio",
            "08" => "Agosto",
            "09" => "Settembre",
            "10" => "Ottobre",
            "11" => "Novembre",
            "12" => "Dicembre",
        );
        $arrayRecensioni = $queryRecensioni->get_result();

        foreach ($arrayRecensioni as $recensione) {
            $data = $recensione['datainserimento'];
            $valutazione = $recensione['valutazione'];
            $commento = $recensione['commento'];

            $listaRecensioni .= "<li";
            if ($cont++ == 0) {
                $listaRecensioni .= " id='primaRecensione'";
            }
            $listaRecensioni .= " class='recensione'>";

            $listaRecensioni .= "<p class='miniGrassetto'>" . $recensione['titolo'] . "</p>";
            $listaRecensioni .= "<a href='libro.php?isbn=" . $recensione['isbn'] . "'>" . $recensione['isbn'] . "</a>";

            // data
            $arrayData = explode("-", $data);
            $anno = $arrayData[0];
            $mese = $arrayData[1];
            $giorno = $arrayData[2];
            $listaRecensioni .= "<p>" . $giorno . " " . $arrayMesi[$mese] . " " . $anno . "</p>";

            // stelle
            $scrittaStella = "stell";
            $scrittaStella .= ($valutazione == 1) ? "a" : "e";
            $listaRecensioni .= "<p><abbr title='"  . $valutazione .  " " . $scrittaStella . " su 5'>";
            for ($i = 0; $i < 5; $i++) {
                if ($i < $valutazione) {
                    $listaRecensioni .= "<i class='fas fa-star starChecked'></i>";
                } else {
                    $listaRecensioni .= "<i class='fas fa-star starNotChecked'></i>";
                }
            }
            $listaRecensioni .= "</abbr></p>";
            $listaRecensioni .= "<p>" . $commento . "</p>";

            $listaRecensioni .= "<form action='eliminaRecensione.php' method='post'>";
            $listaRecensioni .= "<input type='hidden' name='idUtente' value='" . $_SESSION['Codice_identificativo'] . "'/>";
            $listaRecensioni .= "<input type='hidden' name='isbn' value='" . $recensione['libro_isbn'] . "'/>";
            $listaRecensioni .= "<input type='submit' class='button submitEliminaRecensione' value='Elimina recensione'/>";
            $listaRecensioni .= "</form>";

            $listaRecensioni .= "</li>";
        }
        $listaRecensioni .= "</ul>";
    } else {
        $_SESSION["info"] = "Non sono presenti recensioni";
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
    }
    else
    {
        $paginaHTML = str_replace("</alert>", "", $paginaHTML);
    }

    $paginaHTML = str_replace("</listaRecensioni>", $listaRecensioni, $paginaHTML);

    $connessione->closeConnection();
    // -------------------

    echo $paginaHTML;
}
