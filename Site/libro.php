<?php
session_start();

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$paginaHTML = graphics::getPage("libro_php.html");

// Accesso al database

$trovatoErrore = false;

if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];

    $connessione = new Service();
    $a = $connessione->openConnection();

    $queryIsbn = $connessione->get_book_by_isbn($isbn);

    $trovatoErrore = false;

    if ($queryIsbn->ok() && !$queryIsbn->is_empty()) {
        $tmp = $queryIsbn->get_result();
        // Ce un libro con quell'isbn, posso andare avanti

        // ---- IMG LIBRO ----
        $imgLibro = "<img id='imgLibro' src=" . $tmp[0]['Percorso'] . ">";

        // ---- INFO GENERALI ----

        $infoGenerali = "<p id='titolo'>" . $tmp[0]['Titolo'] . "</p>";

        ///
        $infoGenerali .= "<p>";
        foreach ($tmp as $riga) {
            $infoGenerali .= $riga['autore_nome'] . " " . $riga['autore_cognome'] . ", ";
        }
        $infoGenerali = substr($infoGenerali, 0, strlen($infoGenerali) - 2);
        $infoGenerali .= "</p>";

        $offertaQuery = $connessione->get_active_offer_by_isbn($isbn);

        if ($offertaQuery->ok()) {
            $prezzo = number_format((float)$tmp[0]['Prezzo'] * (100 - $offertaQuery->get_result()[0]['sconto']) / 100, 2, '.', '') . " (" . $offertaQuery->get_result()[0]['sconto'] . "% sconto)";
        } else {
            $prezzo = $tmp[0]['Prezzo'];
        }

        ///

        // stelle
        $queryStelle = $connessione->get_avg_review($isbn);

        if ($queryStelle->ok()) {

            if ($queryStelle->get_element_count() > 0) {
                $aux = $queryStelle->get_result();
                $mediaStelle = $aux[0]['media'];
                $roundStelle = ($mediaStelle - floor($mediaStelle) > 0.5) ? ceil($mediaStelle) : floor($mediaStelle);

                //$infoGenerali .= "<li>Valutazione di " . round($queryStelle[0]['media'], 1) . " stelle su 5</li>";

                $infoGenerali .= "<p>"; // . round($queryStelle[0]['media'], 1) . " stelle su 5</li>";

                for ($i = 0; $i < 5; $i++) {
                    if ($i < $roundStelle) {
                        $infoGenerali .= "<i class='fas fa-star starChecked'></i>";
                    } else {
                        $infoGenerali .= "<i class='fas fa-star starNotChecked'></i>";
                    }
                }

                $infoGenerali .= " " . round($mediaStelle, 1) . " su 5</p>";
            } else {
                $infoGenerali .= "<p>Non ci sono recensioni</p>";
            }
        }


        //
        $infoGenerali .= "<p class='miniGrassetto'>&euro;" . $prezzo . "</p>";

        // ---- TRAMA ----
        $trama = "<h3>Descrizione</h3>";
        $trama .= "<p>" . $tmp[0]['Trama'] . "</p>";

        // ---- DETTAGLI LIBRO ----
        $dettagliLibro = "<ul>";
        $dettagliLibro .= "<h3>Informazioni Libro</h3>";
        $dettagliLibro .= "<li><span class='miniGrassetto'>Titolo:</span> " . $tmp[0]['Titolo'] . "</li>";

        // autore
        $dettagliLibro .= "<li><span class='miniGrassetto'>Autore:</span> ";
        foreach ($tmp as $riga) {
            $dettagliLibro .= $riga['autore_nome'] . " " . $riga['autore_cognome'] . ", ";
        }
        $dettagliLibro = substr($dettagliLibro, 0, strlen($dettagliLibro) - 2);
        $dettagliLibro .= "</li>";
        //

        $dettagliLibro .= "<li><span class='miniGrassetto'>Editore:</span> " . $tmp[0]['editore_nome'] . "</li>";

        // data
        //$dettagliLibro .= "<li><span class='miniGrassetto'>Data pubblicazione:</span> " . $queryIsbn[0]['Data_Pubblicazione'] . "</li>";

        $arrayData = explode("-", $tmp[0]['Data_Pubblicazione']);
        $anno = $arrayData[0];
        $mese = $arrayData[1];
        $giorno = $arrayData[2];

        $arrayMesi = array(
            "01" => "Gennaio",
            "02" => "Febbraio",
            "03" => "Marzo",
            "04" => "Aprile",
            "05" => "Maggio",
            "06" => "Giugno",
            "07" => "Luglio",
            "08" => "Agosto",
            "09" => "Settembre",
            "10" => "Ottobre",
            "11" => "Novembre",
            "12" => "Dicembre",
        );

        $dettagliLibro .= "<li><span class='miniGrassetto'>Data pubblicazione:</span> " . $giorno . " " . $arrayMesi[$mese] . " " . $anno . "</li>";
        //

        $dettagliLibro .= "<li><span class='miniGrassetto'>Numero pagine:</span> " . $tmp[0]['Pagine'] . "</li>";

        // generi
        $queryGeneri = $connessione->get_genres_from_isbn($isbn);

        if ($queryGeneri->ok()) {
            $generi = "<li><span class='miniGrassetto'>Gener";
            $generi .= ($queryGeneri->get_element_count() > 1) ? "i:" : "e:";
            $generi .= "</span> ";

            $cont = 0;
            foreach ($queryGeneri->get_result() as $genere) {
                $generi .= $genere['Nome'];

                if (++$cont < $queryGeneri->get_element_count()) {
                    $generi .= ", ";
                }
            }
            $generi .= "</li>";
        } else {
            $trovatoErrore = true;
        }

        $dettagliLibro .= $generi;

        // isbn
        $dettagliLibro .= "<li><span class='miniGrassetto'>ISBN:</span> " . $tmp[0]['ISBN'] . "</li>";

        $dettagliLibro .= "</ul>";

        // ---- QUANTITA ----
        $inputQuantita = "<input type='number' id='quantita' name='quantita' value='1' min='1' step='1' max='" . $tmp[0]['Quantita'] . "'/>";



        // Replace
        $paginaHTML = str_replace("</imgLibro>", $imgLibro, $paginaHTML);
        $paginaHTML = str_replace("</infoGenerali>", $infoGenerali, $paginaHTML);
        $paginaHTML = str_replace("</trama>", $trama, $paginaHTML);
        $paginaHTML = str_replace("</dettagliLibro>", $dettagliLibro, $paginaHTML);
        $paginaHTML = str_replace("</generi>", $generi, $paginaHTML);
        $paginaHTML = str_replace("</inputQuantita>", $inputQuantita, $paginaHTML);
    } else {
        $trovatoErrore = true;
    }
} else {
    $trovatoErrore = true;
}
$connessione->closeConnection();

if ($trovatoErrore) {
    // Errore, pagina senza genereId o con idGenere sbagliato
    header("Location: error.php");
    // $errore = "<img src='images/404.jpg' alt='Errore 404, genere inesistente' id='erroreImg'>";

    // $paginaHTML = str_replace("</listaNuovi>", $errore, $paginaHTML);
    // $paginaHTML = str_replace("</nomeGenere>", "Errore", $paginaHTML);
}

// -------------------

echo $paginaHTML;
