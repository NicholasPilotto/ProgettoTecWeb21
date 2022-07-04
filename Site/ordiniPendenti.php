<?php
if (!isset($_SESSION)) {
  session_start();
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$user = $_SESSION["Codice_identificativo"];
$user = hash('sha256', $user);

$paginaHTML = graphics::getPage("ordiniPendenti_php.html");

if (isset($user) && $user == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
  $connesione = new Service();
  $a = $connesione->openConnection();

  if ($a) {
    $data = $connesione->non_shipped_orders();

    $tabellaOrdini = "";

    if ($data->ok()) {
      $tabellaOrdini = "<table title='Ordini Effettuati'>";
      $tabellaOrdini .=   "<thead>";
      $tabellaOrdini .=       "<tr>";
      $tabellaOrdini .=           "<th scope='col'>Codice Ordine</th>";
      $tabellaOrdini .=           "<th scope='col'>Data Ordine</th>";
      $tabellaOrdini .=           "<th scope='col'>Data Partenza</th>";
      $tabellaOrdini .=           "<th scope='col'>Data Consegna</th>";
      $tabellaOrdini .=           "<th scope='col'>Totale</th>";
      $tabellaOrdini .=           "<th scope='col'>Informazioni Ordine</th>";
      $tabellaOrdini .=           "<th scope='col'>Spedisci Ordine</th>";
      $tabellaOrdini .=       "</tr>";
      $tabellaOrdini .=   "</thead>";

      $tabellaOrdini .=   "<tbody>";

      foreach ($data->get_result() as $ordine) {
        $tabellaOrdini .= "<tr>";
        $tabellaOrdini .=   "<td>" . $ordine['codice_univoco'] . "</td>";
        $tabellaOrdini .=   "<td>" . $ordine['data'] . "</td>";
        $tabellaOrdini .=   "<td>" . (($ordine['data_partenza'] == "") ? "Ordine non spedito" : date_format(date_create($ordine['data_partenza']), 'd/m/Y')) . "</td>";
        $tabellaOrdini .=   "<td>" . (($ordine['data_consegna'] == "") ? "Ordine non spedito" : date_format(date_create($ordine['data_consegna']), 'd/m/Y')) . "</td>";
        $tabellaOrdini .=   "<td>&euro;" . $ordine['totale'] . "</td>";
        $tabellaOrdini .=   "<td><a href='infoOrdine.php?ordine=" . $ordine['codice_univoco'] . "'><abbr title='Visualizza informazioni'><i class='fa fa-info-circle infoOrdine' aria-hidden='true'></i></abbr></a></td>";
        $tabellaOrdini .=   "<td><a href='ship_order.php?ordine=" . $ordine['codice_univoco'] . "'><abbr title='Visualizza informazioni'><i class='fa fa-truck infoOrdine' aria-hidden='true'></i></abbr></a></td>";
        $tabellaOrdini .= "</tr>";
      }

      $tabellaOrdini .=  "</tbody>";
      $tabellaOrdini .= "</table>";

      $paginaHTML = str_replace("</tabellaOrdini>", $tabellaOrdini, $paginaHTML);
    } else {
      $_SESSION["info"] = $data->get_error_message();
    }
  } else {
    $_SESSION["error"] = "Impossibile connettersi al sistema";
  }
} else {
  $_SESSION["error"] = "Non possiedi i permessi necessari.";
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


echo $paginaHTML;
