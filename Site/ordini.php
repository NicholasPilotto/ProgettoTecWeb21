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
        $queryOrdini = $connessione->get_order_by_user($_SESSION["Codice_identificativo"]);

        $tabellaOrdini = "";

        if ($queryOrdini->ok() && !$queryOrdini->is_empty()) {
            $tabellaOrdini = "<table title='Ordini Effettuati'>";
            $tabellaOrdini .=   "<thead>";
            $tabellaOrdini .=       "<tr>";
            $tabellaOrdini .=           "<th scope='col'>Codice Ordine</th>";
            $tabellaOrdini .=           "<th scope='col'>Data Ordine</th>";
            $tabellaOrdini .=           "<th scope='col'>Data Partenza</th>";
            $tabellaOrdini .=           "<th scope='col'>Data Consegna</th>";
            $tabellaOrdini .=           "<th scope='col'>Totale</th>";
            $tabellaOrdini .=           "<th scope='col'>Informazioni Ordine</th>";
            $tabellaOrdini .=       "</tr>";
            $tabellaOrdini .=   "</thead>";

            $tabellaOrdini .=   "<tbody>";

            foreach ($queryOrdini->get_result() as $ordine)
            {
                $tabellaOrdini .= "<tr>";
                $tabellaOrdini .=   "<td>" . $ordine['codice_univoco'] . "</td>";
                $tabellaOrdini .=   "<td>" . $ordine['data'] . "</td>";
                $tabellaOrdini .=   "<td>" . (($ordine['data_partenza'] == "") ? "Ordine non spedito" : $ordine['data_partenza']) . "</td>";
                $tabellaOrdini .=   "<td>" . (($ordine['data_consegna'] == "") ? "Ordine non spedito" : $ordine['data_consegna']) . "</td>";
                $tabellaOrdini .=   "<td>&euro;" . $ordine['totale'] . "</td>";
                $tabellaOrdini .=   "<td><a href='infoOrdine.php?ordine=" . $ordine['codice_univoco'] . "'><abbr title='Visualizza informazioni'><i class='fa fa-info-circle infoOrdine' aria-hidden='true'></i></abbr></a></td>";
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
