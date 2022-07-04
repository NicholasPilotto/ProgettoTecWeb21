<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("infoOrdine_php.html");

// setto sessione per paginaPrecedente, che era stata cancellata in getPage()
$url = explode("/", $_SERVER['REQUEST_URI']);
$current = end($url);

$_SESSION['paginaPrecedente'] = " &gt;&gt; <a href='account.php'>Account</a> &gt;&gt; <a href='ordini.php'>Ordini</a> &gt;&gt; <a href='" . $current . "'>Info Ordine</a>";
// -------------------------------------------------------------------------

$connessione = new Service();
$a = $connessione->openConnection();

$alert = "";

if (isset($_SESSION["error"])) {
  $paginaHTML = str_replace("</totale>", "<span class='alert error'><i class='fa fa-close'  aria-hidden='true'></i> " . $_SESSION["error"] . "</span>", $paginaHTML);
  unset($_SESSION["error"]);
} else if (isset($_SESSION["info"])) {
  $paginaHTML = str_replace("</totale>", "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> " . $_SESSION["info"] . "</span>", $paginaHTML);
  unset($_SESSION["info"]);
} else if (isset($_SESSION["success"])) {
  $paginaHTML = str_replace("</totale>", "<span class='alert success'><i class='fa fa-check' aria-hidden='true'></i> " . $_SESSION["success"] . "</span>", $paginaHTML);
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
          $bookElement .= "<p><span class='miniGrassetto'>ISBN:</span> " . $book["isbn"] . "</p>";
          $bookElement .= "<p><span class='miniGrassetto'>Quantit&agrave;:</span> " . $book["ordQuant"] . "</p>";
          $bookElement .= "<p><span class='miniGrassetto'>Prezzo:</span> &euro;" . $book["prezzo"] . "</p>";
          $bookElement .= "</div>";
          $bookElement .= "</li>";
        }
        $bookElement .= "</ul>";

        $info = "<p class='carrelloStatus'>" . "<span class='boldText'>" . "Indirizzo:</span> " . $orderList->get_result()[0]["via"] . ", " . $orderList->get_result()[0]["num_civico"] .  ", " . $orderList->get_result()[0]["città"] . ", " . $orderList->get_result()[0]["cap"] . "</p>";
        $delivery = isset($orderList->get_result()[0]["data_consegna"]) ? date_format(date_create($orderList->get_result()[0]["data_consegna"]), 'd/m/Y') : "Non ancora inviato";
        $orderedBy = "";
        $delete = "";
        $user = hash('sha256', $user);
        if ($user != "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957")
        {
          if (!isset($orderList->get_result()[0]["data_consegna"]))
          {
            $delete .= "<form class='formOrdine' method='post' action='eliminaOrdine.php'>
                      <input type='submit' class='button eliminaOrdineButton' value='Elimina ordine'/>
                      <input type='hidden' name='orderDelete' value='" . $ordine . "'/>
                      </form>";
          }
        }
        else
        {
          $orderedBy .= "<p class='carrelloStatus'><span class='boldText'>Utente:</span> " . $orderList->get_result()[0]["nome"] . " " . $orderList->get_result()[0]["cognome"] . "</p>";
        }
        $info .= "<p class='carrelloStatus'><span class='boldText'>" . "Data di consegna:</span> " . $delivery . "</p>" . $orderedBy;
        $paginaHTML = str_replace("</indirizzo>", $info, $paginaHTML);
        $totString = "<p class='carrelloStatus'>" . "<span class='boldText'>" . "Prezzo totale:</span> &euro;" . $orderList->get_result()[0]["totale"] . "</p>" . $delete;
        $paginaHTML = str_replace("</riepilogo>", $totString, $paginaHTML);
      } else {
        $alert = graphics::createAlert("info", "Nessun ordine trovato");
      }
    }
  } else {
    $alert = graphics::createAlert("error", "La sessione sembra essere corrotta");
  }
} else {
  $alert = graphics::createAlert("error", "Impossibile connettersi al sistema");
}

$paginaHTML = str_replace("</alert>", $alert, $paginaHTML);
$paginaHTML = str_replace("</listaOrdine>", $bookElement, $paginaHTML);

echo $paginaHTML;