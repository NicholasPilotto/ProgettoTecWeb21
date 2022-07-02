<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    $paginaHTML = graphics::getPage("indirizzi_php.html");

    // Accesso al database
    $connessione = new Service();
    $a = $connessione->openConnection();

    if ($a) {

        $queryIndirizzi = $connessione->get_addresses($_SESSION["Codice_identificativo"]);

        $tabellaIndirizzi = "";

        if ($queryIndirizzi->ok() && !$queryIndirizzi->is_empty()) {
            $tabellaIndirizzi = "<table title='I tuoi indirizzi'>";
            $tabellaIndirizzi .=   "<thead>";
            $tabellaIndirizzi .=       "<tr>";
            $tabellaIndirizzi .=           "<th scope='col'>Citt&agrave;</th>";
            $tabellaIndirizzi .=           "<th scope='col'>Cap</th>";
            $tabellaIndirizzi .=           "<th scope='col'>Via</th>";
            $tabellaIndirizzi .=           "<th scope='col' class='tdthDestra'>Numero Civico</th>";
            $tabellaIndirizzi .=           "</tr>";
            $tabellaIndirizzi .=   "</thead>";

            $tabellaIndirizzi .=   "<tbody>";

            foreach ($queryIndirizzi->get_result() as $indirizzo) {
                $tabellaIndirizzi .= "<tr>";
                $tabellaIndirizzi .=   "<td>" . $indirizzo['città'] . "</td>";
                $tabellaIndirizzi .=   "<td>" . $indirizzo['cap'] . "</td>";
                $tabellaIndirizzi .=   "<td>" . $indirizzo['via'] . "</td>";
                $tabellaIndirizzi .=   "<td class='tdthDestra'>" . $indirizzo['num_civico'] . "</td>";
                $tabellaIndirizzi .= "</tr>";
            }

            $tabellaIndirizzi .=   "</tbody>";

            // tfoot?

            $tabellaIndirizzi .= "</table>";
        } else {
            $tabellaIndirizzi = "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Nessun indirizzo presente</span>";
        }
    } else {
        $tabellaIndirizzi = "<span class='alert error'><i class='fa fa-times' aria-hidden='true'></i> Impossibile connettersi al sistema.</span>";
    }

    $paginaHTML = str_replace("</tabellaIndirizzi>", $tabellaIndirizzi, $paginaHTML);

    $connessione->closeConnection();
    // -------------------

    echo $paginaHTML;
}
