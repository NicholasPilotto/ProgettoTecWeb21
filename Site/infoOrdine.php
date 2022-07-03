<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

// Breadcrumb
$paginaPrecedente = " &gt;&gt; Dettaglio Libro"; // caso dalla home
if (isset($_SESSION["paginaPrecedente"])) {
  $paginaPrecedente = $_SESSION["paginaPrecedente"];
  $paginaPrecedente .= " &gt;&gt; Libro";

  unset($_SESSION['paginaPrecedente']);
}

// prima di fare il getPage() che cancella la sessione, prendo da che pagina vengo, e poi cancello la sessione
$paginaHTML = graphics::getPage("infoOrdine_php.html");

// replace della breadcrumb
$paginaHTML = str_replace("</paginaPrecedente>", $paginaPrecedente, $paginaHTML);

$connessione = new Service();
$a = $connessione->openConnection();

$alert = "";

if (isset($_SESSION["error"])) {
  $paginaHTML = str_replace("</totale>", "<span class='alert error'><i class='fa fa-close'  aria-hidden='true'></i> " . $_SESSION["error"] . "</span></br>", $paginaHTML);
  unset($_SESSION["error"]);
} else if (isset($_SESSION["info"])) {
  $paginaHTML = str_replace("</totale>", "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> " . $_SESSION["info"] . "</span></br>", $paginaHTML);
  unset($_SESSION["info"]);
} else if (isset($_SESSION["success"])) {
  $paginaHTML = str_replace("</totale>", "<span class='alert success'><i class='fa fa-check' aria-hidden='true'></i> " . $_SESSION["success"] . "</span></br>", $paginaHTML);
  unset($_SESSION["success"]);
}

if ($a) {
  $user = $_SESSION["Codice_identificativo"];
  $ordine = $_GET["ordine"];
  if ($user) {
    if ($ordine) {
      $orderList = $connessione->get_order_books($ordine);
      $bookElement = "";
      if ($orderList->ok()) {
        $bookElement .= "<ul class='carrelloCards'>";
        foreach ($orderList->get_result() as $book) {
          $bookElement .= "<li>";
          $bookElement .= "<a href='libro.php?isbn=" . $book["isbn"] . "'><img class='carrelloImg' src='" . $book['percorso'] . "' alt=''></a>";
          $bookElement .= "<div>";
          $bookElement .= "<a class='titolo' href='libro.php?isbn=" . $book["isbn"] . "'>" . $book['titolo'] . "</a>";
          $bookElement .= "<p>ISBN: " . $book["isbn"] . "</p>";
          $bookElement .= "<p>Quantit&agrave;: " . $book["ordQuant"] . "</p>";
          $bookElement .= "<p>Prezzo: &euro;" . $book["prezzo"] . "</p>";
          // $carrelloDiv .= "<p>Costo totale: &euro;" . $data->total . "</p>";
          // $carrelloDiv .= "<form method='post' id='removeCartForm' action='removecart.php?isbn=" . $isbn . "'><input class='button cartButton' type='submit' value='Rimuovi'/></form>";
          $bookElement .= "</div>";
          $bookElement .= "</li>";
        }
        $bookElement .= "</ul>";
        $info = "<div class='carrelloStatus'><p>" . "Indirizzo: " . $orderList->get_result()[0]["via"] . ", " . $orderList->get_result()[0]["num_civico"] .  ", " . $orderList->get_result()[0]["città"] . ", " . $orderList->get_result()[0]["cap"] . "</p>" . $purchase . "</div>";
        $delivery = isset($orderList->get_result()[0]["data_consegna"]) ? date_format(date_create($orderList->get_result()[0]["data_consegna"]), 'd/m/Y') : "Non ancora inviato";
        $orderedBy = "";
        $user = hash('sha256', $user);
        if ($user != "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
          if (!isset($orderList->get_result()[0]["data_consegna"])) {
            $delete .= "<form method='post' action='eliminaOrdine.php'>
                      <input type='submit' class='button procediAcquistoButton' value='Elimina ordine'/>
                      <input type='hidden' name='orderDelete' value='" . $ordine . "'/>
                      </form>";
          }
        } else {
          $orderedBy .= "<p>Utente: " . $orderList->get_result()[0]["nome"] . " " . $orderList->get_result()[0]["cognome"] . "</p>";
        }
        $info .= "<div class='carrelloStatus'><p>" . "Data di consegna: " . $delivery . "</p>" . $orderedBy . "</div>";
        $paginaHTML = str_replace("</indirizzo>", $info, $paginaHTML);
        $delete = "";
        $totString .= "<div class='carrelloStatus'><p>" . "Prezzo totale: &euro;" . $orderList->get_result()[0]["totale"] . "</p>" . $delete . "</div>";
        $paginaHTML = str_replace("</riepilogo>", $totString, $paginaHTML);
      } else {
        $alert = "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Nessun ordine trovato.</span>";
      }
    }
  } else {
    $alert = "<span class='alert error'><i class='fa fa-times' aria-hidden='true'></i> La sessione sembra essere corrotta.</span>";
  }
} else {
  $alert = "<span class='alert error'><i class='fa fa-times' aria-hidden='true'></i> Impossibile connettersi al sistema</span>";
}

$paginaHTML = str_replace("</alert>", $alert, $paginaHTML);
$paginaHTML = str_replace("</listaOrdine>", $bookElement, $paginaHTML);

echo $paginaHTML;
