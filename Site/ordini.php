<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    $paginaHTML = graphics::getPage("ordini_php.html");

    // Accesso al database
    $connessione = new Service();
    $a = $connessione->openConnection();

    if ($a) {
        $queryOrdini = $connessione->get_order_books($_SESSION["Codice_identificativo"]);

        $tabellaOrdini = "";

        if ($queryOrdini->ok() && !$queryOrdini->is_empty()) {
            $tabellaOrdini = "<table title='Ordini Effettuati'>";
            $tabellaOrdini .=   "<thead>";
            $tabellaOrdini .=       "<tr>";
            $tabellaOrdini .=           "<th scope='col'>Codice Ordine</th>";
            $tabellaOrdini .=           "<th scope='col'>ISBN</th>";
            $tabellaOrdini .=           "<th scope='col'>Titolo</th>";
            $tabellaOrdini .=           "<th scope='col'>Quantit&agrave;</th>";
            $tabellaOrdini .=           "<th scope='col'>Data Ordine</th>";
            $tabellaOrdini .=           "<th scope='col'>Data Consegna</th>";
            $tabellaOrdini .=           "<th scope='col' class='tdthDestra'>Totale</th>";
            $tabellaOrdini .=           "</tr>";
            $tabellaOrdini .=   "</thead>";

            $tabellaOrdini .=   "<tbody>";

            // Calcolo rowspan
            $rowspans = array_count_values(array_column($queryOrdini->get_result(), 'codice_univoco'));

            foreach ($queryOrdini->get_result() as $ordine) {
                $rs = 0;
                if (array_key_exists($ordine['codice_univoco'], $rowspans)) {
                    $rs = $rowspans[$ordine['codice_univoco']];
                    unset($rowspans[$ordine['codice_univoco']]);
                }

                $tabellaOrdini .= "<tr>";

                if ($rs > 0) {
                    $tabellaOrdini .=   "<td rowspan='" . $rs . "'>" . $ordine['codice_univoco'] . "</td>";
                }

                $tabellaOrdini .=   "<td><a href='libro.php?isbn=" . $ordine['isbn'] . "'>" . $ordine['isbn'] . "</a></td>";
                $tabellaOrdini .=   "<th scope='row'>" . $ordine['titolo'] . "</th>";
                $tabellaOrdini .=   "<td>" . $ordine['quantita'] . "</td>";

                if ($rs > 0) {
                    $tabellaOrdini .=   "<td rowspan='" . $rs . "'>" . $ordine['data'] . "</td>";
                    $tabellaOrdini .=   "<td rowspan='" . $rs . "'>" . $ordine['data_consegna'] . "</td>";
                    $tabellaOrdini .=   "<td rowspan='" . $rs . "' class='tdthDestra'>&euro;" . $ordine['totale'] . "</td>";
                }
                $tabellaOrdini .= "</tr>";
            }
            $tabellaOrdini .=  "</tbody>";

            $tabellaOrdini .= "</table>";
        } else {
            $tabellaOrdini = "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Nessun ordine presente</span></br>";
        }
    } else {
        $tabellaOrdini = "<span class='alert error'><i class='fa fa-close' aria-hidden='true'></i> Impossibile connettersi al sistema</span></br>";
    }
    $paginaHTML = str_replace("</tabellaOrdini>", $tabellaOrdini, $paginaHTML);

    $connessione->closeConnection();
    // -------------------

    echo $paginaHTML;
}
