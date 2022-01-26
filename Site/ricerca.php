<?php
session_start();

class CoppiaRicerca {
  public $isbn;
  public $value;
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("ricerca_php.html");

// Stampa filtri GENERE

$generi = array(
  "genere10" => "Storia e Biografie",
  "genere11" => "Fumetti e Manga",
  "genere12" => "Classici e Romanzi",
  "genere13" => "Avventura e Azione",
  "genere14" => "Scuole e Universit&agrave;",
  "genere15" => "Arte e Tempo Libero",

  "genere16" => "Filosofia e Psicologia",
  "genere17" => "Scienza e Fantascienza",
  "genere18" => "Economia e Business",
  "genere19" => "Dizionari ed Enciclopedie",
  "genere20" => "Medicina e Salute",
  "genere21" => "Bambini e Ragazzi",
);

$listaFiltriGenere =  "<ul>";

foreach ($generi as $key => $value) {
  $listaFiltriGenere .= "<li>";

  $listaFiltriGenere .= "<input type='checkbox' id='" . $key . "' name='" . $key . "' ";

  if (isset($_GET[$key]) && $_GET[$key] == "on") {
    $listaFiltriGenere .= "checked";
  }

  $listaFiltriGenere .= "/>";

  $listaFiltriGenere .= "<label for=" . $key . ">" . $value . "</label>";

  $listaFiltriGenere .= "</li>";
}

$listaFiltriGenere .= "</ul>";

$paginaHTML = str_replace("</listaFiltriGenere>", $listaFiltriGenere, $paginaHTML);

// Accesso al database
$connessione = new Service();
$a = $connessione->openConnection();

$queryLibri = $connessione->get_all_books();

$search = isset($_GET['search']) ? $_GET['search'] : "";

$arrayDistanze = array();
$arrayAutori = array();

// controllo se è un isbn
$isISBN = (is_numeric($search) && strlen($search) == 13);

if ($isISBN) {
  // lo cerco nel db
  $queryIsbn = $connessione->get_book_by_isbn($search);

  if ($queryIsbn->ok()) {

    if ($queryIsbn->get_element_count() > 0) {
      $coppiaISBN = new CoppiaRicerca();
      $coppiaISBN->isbn = $search;
      $coppiaISBN->value = 0; // distanza minima

      array_push($arrayDistanze, $coppiaISBN);
    } else {
      // errore, non c'è un libro con quell' isbn
    }
  }
} else {
  $coppieAggiunte = array();
  foreach ($queryLibri as $libro) {
    $titolo = strip_tags($libro['Titolo']);
    $autore = strip_tags($libro['autore_nome']) . " " . strip_tags($libro['autore_cognome']);

    if (!in_array($libro['ISBN'], $coppieAggiunte)) {
      array_push($coppieAggiunte, $libro['ISBN']);

      $coppiaTitolo = new CoppiaRicerca();
      $coppiaTitolo->isbn = $libro['ISBN'];
      $coppiaTitolo->value = levenshtein(strtoupper($search), strtoupper($titolo), 0, 4, 4);

      array_push($arrayDistanze, $coppiaTitolo);

      $arrayAutori[$libro['ISBN']] = "";
    }
    $coppiaAutore = new CoppiaRicerca();
    $coppiaAutore->isbn = $libro['ISBN'];
    $coppiaAutore->value = levenshtein(strtoupper($search), strtoupper($autore), 0, 4, 4);

    $arrayAutori[$libro['ISBN']] .= $autore . ", ";

    array_push($arrayDistanze, $coppiaAutore);
  }
}

usort($arrayDistanze, function ($a, $b) {
  return $a->value - $b->value;
}); // ordino per valore

//asort($arrayDistanze); // ordino per valore

$cont = 0;
$limit = 5;
$daTogliere = array("_ISBN", "_titolo", "_autore");
$libriAggiunti = array();

$libriTrovati = "<ul class='advertsCards'>";

foreach ($arrayDistanze as $coppia) {
  if ($cont == $limit) break; // ho preso tutti i libri che voglio, esco dal ciclo

  $key = $coppia->isbn;

  if (!in_array($key, $libriAggiunti)) {
    array_push($libriAggiunti, $key);

    foreach ($queryLibri as $libro) {
      if ($libro['ISBN'] == $key) {
        $libriTrovati .= "<li class='imgLi";
        if ($cont == 0) {
          $libriTrovati .= " primaInserzione";
        }
        $libriTrovati .= "'><a href='libro.php?isbn=" . $key . "'><img class='advertsImg' src='" . $libro['Percorso'] . "' alt=''></a></li>";

        $libriTrovati .= "<li class='textLi";
        if ($cont == 0) {
          $libriTrovati .= " primaInserzione";
        }
        $libriTrovati .= "'>";

        $libriTrovati .= "<a class='titolo' href='libro.php?isbn=" . $key . "'>" . $libro['Titolo'] . "</a>";
        $libriTrovati .= "<p>" . substr($arrayAutori[$key], 0, strlen($arrayAutori[$key]) - 2) . "</p>";
        $libriTrovati .= "<p>&euro;" . $libro['Prezzo'] . "</p>";

        $libriTrovati .= "</li>";

        $cont++;

        break; // ho trovato il libro, esco dal ciclo
      }
    }
  }
}

$libriTrovati .= "</ul>";

$paginaHTML = str_replace("</libriTrovati>", $libriTrovati, $paginaHTML);

$connessione->closeConnection();
// -------------------

echo $paginaHTML;
