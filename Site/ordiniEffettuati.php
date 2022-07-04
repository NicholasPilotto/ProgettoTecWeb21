<?php
session_start();

use DB\Service;

require_once('backend/db.php');
require_once "graphics.php";

$user = $_SESSION["Codice_identificativo"];
$user = hash('sha256', $user);

if (isset($user) && $user == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
  $paginaHTML = graphics::getPage("ordini_php.html");

  // Accesso al database
  $connessione = new Service();
  $a = $connessione->openConnection();

  if ($a) {
    $queryOrdini = $connessione->get_orders_non_shipped();

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
      $tabellaOrdini = graphics::createAlert("info", "Nessun ordine presente");
    }
  } else {
    $tabellaOrdini = graphics::createAlert("error", "Impossibile connettersi al sistema");
  }
  $paginaHTML = str_replace("</tabellaOrdini>", $tabellaOrdini, $paginaHTML);

  $connessione->closeConnection();
  // -------------------

  echo $paginaHTML;
}
else
{
  header("Location: index.php");
}