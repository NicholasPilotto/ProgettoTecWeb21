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

    $tabellaOrdini = "";

    if ($a) {
        $queryOrdini = $connessione->get_order_by_user($_SESSION["Codice_identificativo"]);

        if ($queryOrdini->ok() && !$queryOrdini->is_empty()) {
            $tabellaOrdini = "<table class='tabellaOrdini' id='tabellaOrdini' title='Ordini Effettuati'>";
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

            foreach ($queryOrdini->get_result() as $ordine) {
                $tabellaOrdini .= "<tr>";
                $tabellaOrdini .=   "<td data-label='Codice Ordine'>" . $ordine['codice_univoco'] . "</td>";
                $tabellaOrdini .=   "<td data-label='Data Ordine'>" . date_format(date_create($ordine['data']), 'd/m/Y') . "</td>";
                $tabellaOrdini .=   "<td data-label='Data Partenza'>" . (($ordine['data_partenza'] == "") ? "Ordine non spedito" : date_format(date_create($ordine['data_partenza']), 'd/m/Y')) . "</td>";
                $tabellaOrdini .=   "<td data-label='Data Consegna'>" . (($ordine['data_consegna'] == "") ? "Ordine non spedito" : date_format(date_create($ordine['data_consegna']), 'd/m/Y')) . "</td>";
                $tabellaOrdini .=   "<td data-label='Totale'>&euro;" . $ordine['totale'] . "</td>";
                $tabellaOrdini .=   "<td data-label='Informazioni Ordine'><a href='infoOrdine.php?ordine=" . $ordine['codice_univoco'] . "'><abbr title='Visualizza informazioni'><i class='fa fa-info-circle infoOrdine' aria-hidden='true'></i></abbr></a></td>";
                $tabellaOrdini .= "</tr>";
            }

            $tabellaOrdini .=  "</tbody>";
            $tabellaOrdini .= "</table>";
        } else {
            $_SESSION["info"] = "Nessun ordine presente";
        }
    }
    else
    {
        $_SESSION["error"] = "Impossibile connettersi al sistema";
    }

    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("error", $_SESSION["error"]), $paginaHTML);
        unset($_SESSION["error"]);
    }
    if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("info", $_SESSION["info"]), $paginaHTML);
        unset($_SESSION["info"]);
    }
    if (isset($_SESSION["success"]))
    {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("success", $_SESSION["success"]), $paginaHTML);
        unset($_SESSION["success"]);
    }
    else
    {
        $paginaHTML = str_replace("</alert>", "", $paginaHTML);
    }

    $paginaHTML = str_replace("</tabellaOrdini>", $tabellaOrdini, $paginaHTML);

    $connessione->closeConnection();
    // -------------------

    echo $paginaHTML;
}
