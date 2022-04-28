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

// setto sessione per paginaPrecedente, che era stata cancellata in getPage()
$url = explode("/", $_SERVER['REQUEST_URI']);
$current = end($url);

$_SESSION['paginaPrecedente'] = " &gt;&gt; <a href='" . $current . "'>Ricerca</a>";
// -------------------------------------------------------------------------

$generiOn = array();

$search = isset($_GET['search']) ? $_GET['search'] : "";
// Stampa barra di ricerca
$barraRicerca = "<input type='search' id='searchBar' name='search' placeholder='Titolo, autore o ISBN' value='" . $search . "'/>";
$paginaHTML = str_replace("</barraRicerca>", $barraRicerca, $paginaHTML);

// Stampa filtri PREZZO
$prezzoMin = 1;
$prezzoMax = 20;
if (isset($_GET['prezzoMin']) && $_GET['prezzoMin'] != "") {
    $prezzoMin = $_GET['prezzoMin'];
}
if (isset($_GET['prezzoMax']) && $_GET['prezzoMax'] != "") {
    $prezzoMax = $_GET['prezzoMax'];
}
$listaFiltriPrezzo = "<ul>";

$listaFiltriPrezzo .= "<li>";
$listaFiltriPrezzo .= "<input type='number' id='prezzoMin' name='prezzoMin' min='0.00' max='100' step='any'  value='" . $prezzoMin . "'/>";

$listaFiltriPrezzo .= "<label for='prezzoMin'>&euro; Min</label>";
$listaFiltriPrezzo .= "</li>";

$listaFiltriPrezzo .= "<li>";
$listaFiltriPrezzo .= "<input type='number' id='prezzoMax' name='prezzoMax' min='0.00' max='100' step='any'  value='" . $prezzoMax . "'/>";

$listaFiltriPrezzo .= "<label for='prezzoMax'>&euro; Max</label>";
$listaFiltriPrezzo .= "</li>";

$listaFiltriPrezzo .= "</ul>";

$paginaHTML = str_replace("</listaFiltriPrezzo>", $listaFiltriPrezzo, $paginaHTML);

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

        array_push($generiOn, $key);
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

if ($queryLibri->ok()) {
    // SE NON E' VUOTO NON CERCARE NULLA

    $arrayDistanze = array();
    $arrayAutori = array();

    // controllo se è un isbn
    $isISBN = (is_numeric($search) && strlen($search) == 13);

    if ($isISBN) {
        // lo cerco nel db
        $queryIsbn = $connessione->get_book_by_isbn($search);

        if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
            // controllo che sia del prezzo giusto
            $skip = false;

            $prezzoConfronto = $queryIsbn->get_result()[0]['prezzo'];

            if (isset($queryIsbn->get_result()[0]['sconto'])) {
                $prezzoConfronto = number_format((float)$queryIsbn->get_result()[0]['prezzo'] * (100 - $queryIsbn->get_result()[0]['sconto']) / 100, 2, '.', '');
            }

            if ($prezzoConfronto < $prezzoMin) {
                // non va bene
                $skip = true;
            }
            if ($prezzoConfronto > $prezzoMax) {
                // non va bene
                $skip = true;
            }

            if (!$skip) {
                $coppiaISBN = new CoppiaRicerca();
                $coppiaISBN->isbn = $search;
                $coppiaISBN->value = 0; // distanza minima

                array_push($arrayDistanze, $coppiaISBN);

                $autore = "";
                foreach ($queryIsbn->get_result() as $libro) {
                    $autore .= $libro['autore_nome'] . " " . $libro['autore_cognome'] . ", ";
                }
                $autore = substr($autore, 0, -2);

                $arrayAutori[$queryIsbn->get_result()[0]['isbn']] = $autore;
            }
        } else {
            // errore, non c'è un libro con quell' isbn
        }
    } else {
        $coppieAggiunte = array();
        foreach ($queryLibri->get_result() as $libro) {
            $titolo = strip_tags($libro['titolo']);
            $autore = strip_tags($libro['autore_nome']) . " " . strip_tags($libro['autore_cognome']);

            // controllo che sia del prezzo giusto
            $skip = false;

            $prezzoConfronto = $libro['prezzo'];

            if (isset($libro['sconto'])) {
                $prezzoConfronto = number_format((float)$libro['prezzo'] * (100 - $libro['sconto']) / 100, 2, '.', '');
            }

            if ($prezzoConfronto < $prezzoMin) {
                // non va bene
                $skip = true;
            }
            if ($prezzoConfronto > $prezzoMax) {
                // non va bene
                $skip = true;
            }

            if (!$skip) {
                if (!in_array($libro['isbn'], $coppieAggiunte)) {
                    array_push($coppieAggiunte, $libro['isbn']);

                    $coppiaTitolo = new CoppiaRicerca();
                    $coppiaTitolo->isbn = $libro['isbn'];
                    $coppiaTitolo->value = levenshtein(strtoupper($search), strtoupper($titolo), 0, 4, 4);

                    array_push($arrayDistanze, $coppiaTitolo);

                    $arrayAutori[$libro['isbn']] = "";
                }
                $coppiaAutore = new CoppiaRicerca();
                $coppiaAutore->isbn = $libro['isbn'];
                $coppiaAutore->value = levenshtein(strtoupper($search), strtoupper($autore), 0, 4, 4);

                $arrayAutori[$libro['isbn']] .= $autore . ", ";

                array_push($arrayDistanze, $coppiaAutore);
            }
        }
    }

    usort($arrayDistanze, function ($a, $b) {
        return $a->value - $b->value;
    }); // ordino per valore

    $cont = 0;
    $limit = 5;
    $daTogliere = array("_ISBN", "_titolo", "_autore");
    $libriAggiunti = array();

    $libriTrovati = "<ul class='advertsCards'>";

    foreach ($arrayDistanze as $coppia) {
        if ($cont == $limit) break; // ho preso tutti i libri che voglio, esco dal ciclo

        $key = $coppia->isbn;

        // controllo se ci sono generi selezionati
        $skip = false;
        if (count($generiOn) > 0) {
            // controllo se è del genere giusto
            $generiLibro = $connessione->get_genres_from_isbn($key);
            if ($generiLibro->ok()) {
                foreach ($generiLibro->get_result() as $genere) {
                    if (in_array("genere" . $genere['codice_categoria'], $generiOn)) {
                        $skip = false;
                        break;
                    } else {
                        $skip = true;
                    }
                }
            }
        }

        if (!in_array($key, $libriAggiunti) && !$skip) {
            array_push($libriAggiunti, $key);

            foreach ($queryLibri->get_result() as $libro) {
                if ($libro['isbn'] == $key) {
                    $libriTrovati .= "<li class='imgLi";
                    if ($cont == 0) {
                        $libriTrovati .= " primaInserzione";
                    }
                    $libriTrovati .= "'><a href='libro.php?isbn=" . $key . "'><img class='advertsImg' src='" . $libro['percorso'] . "' alt=''></a></li>";

                    $libriTrovati .= "<li class='textLi";
                    if ($cont == 0) {
                        $libriTrovati .= " primaInserzione";
                    }
                    $libriTrovati .= "'>";

                    $libriTrovati .= "<a class='titolo' href='libro.php?isbn=" . $key . "'>" . $libro['titolo'] . "</a>";
                    $libriTrovati .= "<p>" . substr($arrayAutori[$key], 0, -2) . "</p>";

                    if (isset($libro['sconto'])) {
                        $libriTrovati .= "<p>&euro;" . number_format((float)$libro['prezzo'] * (100 - $libro['sconto']) / 100, 2, '.', '') . " (" . $libro['sconto'] . "% sconto)" . "</p>";
                    } else {
                        $libriTrovati .= "<p>&euro;" . $libro['prezzo'] . "</p>";
                    }

                    $libriTrovati .= "</li>";

                    $cont++;

                    break; // ho trovato il libro, esco dal ciclo
                }
            }
        }
    }

    $libriTrovati .= "</ul>";

    $paginaHTML = str_replace("</libriTrovati>", $libriTrovati, $paginaHTML);
}

$connessione->closeConnection();
// -------------------

echo $paginaHTML;
