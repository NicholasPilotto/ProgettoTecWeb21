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
}

// non sto modificando un libro
unset($_SESSION["editFlag"]);

// prima di fare il getPage() che cancella la sessione, prendo da che pagina vengo, e poi cancello la sessione
$paginaHTML = graphics::getPage("libro_php.html");

// replace della breadcrumb
$paginaHTML = str_replace("</paginaPrecedente>", $paginaPrecedente, $paginaHTML);

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

        $_SESSION["isbncart"] = $isbn;
        $_SESSION["pricecart"] = $tmp[0]["prezzo"];

        // ---- IMG LIBRO ----
        $imgLibro = "<img id='imgLibro' alt='' src='" . $tmp[0]['percorso'] . "'>";

        // ---- INFO GENERALI ----

        $infoGenerali = "<p id='titoloLibro'>" . $tmp[0]['titolo'] . "</p>";

        ///
        $infoGenerali .= "<p>";
        foreach ($tmp as $riga) {
            $cognome = ($riga['autore_cognome'] != "-") ? $riga['autore_cognome'] : "";
            $infoGenerali .= $riga['autore_nome'] . " " . $cognome . ", ";
        }
        $infoGenerali = substr($infoGenerali, 0, strlen($infoGenerali) - 2);
        $infoGenerali .= "</p>";

        $offertaQuery = $connessione->get_active_offer_by_isbn($isbn);
        $sconto = false;

        if ($offertaQuery->ok()) {
            $sconto = true;
            $prezzo = number_format((float)$tmp[0]['prezzo'] * (100 - $offertaQuery->get_result()[0]['sconto']) / 100, 2, '.', '') . " (" . $offertaQuery->get_result()[0]['sconto'] . "% sconto)";
            $_SESSION["pricecart"] = $prezzo;
        } else {
            $prezzo = $tmp[0]['prezzo'];
        }

        ///

        // stelle
        $queryStelle = $connessione->get_avg_review($isbn);

        if ($queryStelle->ok()) {

            if ($queryStelle->get_element_count() > 0) {
                $aux = $queryStelle->get_result();
                $mediaStelle = $aux[0]['media'];
                $roundStelle = ($mediaStelle - floor($mediaStelle) > 0.5) ? ceil($mediaStelle) : floor($mediaStelle);

                $scrittaStella = "stell";
                $scrittaStella .= (round($mediaStelle, 1) == 1) ? "a" : "e";

                $infoGenerali .= "<p><abbr title='" . round($mediaStelle, 1) . " " . $scrittaStella . " su 5'>";

                for ($i = 0; $i < 5; $i++) {
                    if ($i < $roundStelle) {
                        $infoGenerali .= "<i class='fas fa-star starChecked'></i>";
                    } else {
                        $infoGenerali .= "<i class='fas fa-star starNotChecked'></i>";
                    }
                }

                $infoGenerali .= "</abbr></p>";
            } else {
                $infoGenerali .= "<p>Non ci sono recensioni</p>";
            }
        }

        //
        if ($sconto) {
            $prezzoVecchio = $tmp[0]['prezzo'];
            $infoGenerali .= "<p class='miniGrassetto'>Sconto da <del>&euro;" . $prezzoVecchio . "</del> a &euro;" . $prezzo . "</p>";
        } else {
            $infoGenerali .= "<p class='miniGrassetto'>&euro;" . $prezzo . "</p>";
        }

        // ---- TRAMA ----
        $trama = "<h3>Descrizione</h3>";
        $trama .= "<p>" . $tmp[0]['trama'] . "</p>";

        // ---- DETTAGLI LIBRO ----
        $dettagliLibro = "<h3>Informazioni Libro</h3>";
        $dettagliLibro .= "<ul>";
        $dettagliLibro .= "<li><span class='miniGrassetto'>Titolo:</span> " . $tmp[0]['titolo'] . "</li>";

        // autore
        $dettagliLibro .= "<li><span class='miniGrassetto'>Autore:</span> ";
        foreach ($tmp as $riga) {
            $cognome = ($riga['autore_cognome'] != "-") ? $riga['autore_cognome'] : "";
            $dettagliLibro .= $riga['autore_nome'] . " " . $cognome . ", ";
        }
        $dettagliLibro = substr($dettagliLibro, 0, strlen($dettagliLibro) - 2);
        $dettagliLibro .= "</li>";
        //

        $dettagliLibro .= "<li><span class='miniGrassetto'>Editore:</span> " . $tmp[0]['editore_nome'] . "</li>";

        // data

        $arrayData = explode("-", $tmp[0]['data_pubblicazione']);
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

        $dettagliLibro .= "<li><span class='miniGrassetto'>Numero pagine:</span> " . $tmp[0]['pagine'] . "</li>";

        // generi
        $queryGeneri = $connessione->get_genres_from_isbn($isbn);

        if ($queryGeneri->ok()) {
            $generi = "<li><span class='miniGrassetto'>Gener";
            $generi .= ($queryGeneri->get_element_count() > 1) ? "i:" : "e:";
            $generi .= "</span> ";

            $cont = 0;
            foreach ($queryGeneri->get_result() as $genere) {
                $generi .= $genere['nome'];

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
        $dettagliLibro .= "<li><span class='miniGrassetto'>ISBN:</span> " . $tmp[0]['isbn'] . "</li>";

        $dettagliLibro .= "</ul>";

        // ---- QUANTITA ----
        $inputQuantita = "<input type='number' id='quantita' name='quantita' value='1' min='1' step='1' max='" . ceil($tmp[0]['quantita'] * 0.25) . "'/>";

        // ---- RECENSIONI ----
        $queryRecensioni = $connessione->get_reviews_by_isbn($isbn);

        $listaRecensioni = "";
        $cont = 0;
        $maxRec = 8;

        if (count($queryRecensioni->get_result()) != 0) {
            $arrayRecensioni = $queryRecensioni->get_result();

            // cerca se esiste una recensione fatta dall'utente loggato, così da poterla mettere per prima
            if (isset($_SESSION["Codice_identificativo"])) {
                $idUtente = $_SESSION['Codice_identificativo'];
                $recensioneMia = array_filter(
                    $arrayRecensioni,
                    function ($e) use (&$idUtente) {
                        return $e['idUtente'] == $idUtente;
                    }
                );
                if (count($recensioneMia) > 0) {
                    $key = array_keys($recensioneMia)[0];
                    // tolgo la recensione
                    unset($arrayRecensioni[$key]);
                    // la rimetto all'inizio dell'array
                    array_unshift($arrayRecensioni, $recensioneMia[$key]);
                }
            }

            $listaRecensioni .= "<ul id='listaRecensioni' title='Lista recensioni'>";
            foreach ($arrayRecensioni as $recensione) {
                if ($cont >= $maxRec) {
                    break;
                }
                $queryUtente = $connessione->get_utente_by_id($recensione['idUtente']);

                // if ok
                $nomeUtente = $queryUtente->get_result()[0]['username'];
                //$data = date_format(date_create($recensione['datainserimento']), 'd/m/Y');
                $data = $recensione['datainserimento'];
                $valutazione = $recensione['valutazione'];
                $commento = $recensione['commento'];

                // -- BADGE --
                $queryBadge = $connessione->get_reward_badge($recensione['idUtente']);
                // if ok
                $numeroOrdini = $queryBadge->get_result()[0]['total'];

                $lv = 0;
                if ($numeroOrdini >= 15) {
                    $lv = 3;
                    $numeroOrdini = 15;
                } else if ($numeroOrdini >= 10) {
                    $lv = 2;
                    $numeroOrdini = 10;
                } else if ($numeroOrdini >= 5) {
                    $lv = 1;
                    $numeroOrdini = 5;
                }

                $badge = "";
                if ($lv > 0) {
                    $badge = "<abbr title='Badge livello " . $lv . ": questo utente ha effettuato più di " . $numeroOrdini . " ordini'><i class='fas fa-award badgeLv" . $lv . "'></i></abbr>";
                }

                // -----------

                $listaRecensioni .= "<li";

                if ($cont++ == 0) {
                    $listaRecensioni .= " id='primaRecensione'";
                }

                $listaRecensioni .= " class='recensione'>";

                $listaRecensioni .= "<p class='miniGrassetto'>" . $nomeUtente . " " . $badge . "</p>";

                // data
                $arrayData = explode("-", $data);
                $anno = $arrayData[0];
                $mese = $arrayData[1];
                $giorno = $arrayData[2];
                $listaRecensioni .= "<p>" . $giorno . " " . $arrayMesi[$mese] . " " . $anno . "</p>";

                // stelle
                $scrittaStella = "stell";
                $scrittaStella .= ($valutazione == 1) ? "a" : "e";
                $listaRecensioni .= "<p><abbr title='"  . $valutazione .  " " . $scrittaStella . " su 5'>";

                for ($i = 0; $i < 5; $i++) {
                    if ($i < $valutazione) {
                        $listaRecensioni .= "<i class='fas fa-star starChecked'></i>";
                    } else {
                        $listaRecensioni .= "<i class='fas fa-star starNotChecked'></i>";
                    }
                }

                $listaRecensioni .= "</abbr></p>";

                $listaRecensioni .= "<p>" . $commento . "</p>";

                $listaRecensioni .= "</li>";
            }
            $listaRecensioni .= "</ul>";
        } else {
            $listaRecensioni = graphics::createAlert("info", "Nessuna recensione presente");
        }
        

        // Link lascia recensione
        // se l'utente è loggato, può recensire, altrimenti viene mandato al login
        $l = "accedi.php";
        $linkLasciaRecensione = "";
        if (isset($_SESSION["Nome"])) {
            $rev = $connessione->get_review_by_user_book($_SESSION["Codice_identificativo"], $isbn);
            if ($rev->is_empty()) {
                $l = "lasciaRecensione.php?isbn=" . $isbn;
                $linkLasciaRecensione = "<a id='linkLasciaRecensione' href='" . $l . "'>Lascia una recensione</a>";
            }
        }

        // FORM UTENTE O ADMIN
        $codiceIdentificativo = "";
        if (isset($_SESSION["Codice_identificativo"])) {
            $codiceIdentificativo = $_SESSION["Codice_identificativo"];
            $codiceIdentificativo = hash('sha256', $codiceIdentificativo);
        } else {
            $formBottoni = "";
        }

        if ($codiceIdentificativo != "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
            // utente
            // controllo se è loggato:
            $wishButton = "";
            if($codiceIdentificativo != "")
            {
                // è loggato, mostro la wishlist
                $wish = $connessione->get_wishlist($_SESSION["Codice_identificativo"]);
                $list = array();
                foreach ($wish->get_result() as $w) {
                    array_push($list, $w["libro_isbn"]);
                }
                $present = in_array($isbn, $list);
                if (!$present) {
                    $wishButton = "<input name='aggiungiWhish' type='submit' class='button' value='Aggiungi alla wishlist' />";
                }
            }

            $formBottoni = "
            <form action='addcart.php' method='post'>
                <input name='aggiungiCarrello' type='submit' class='button' value='Aggiungi al carrello' />" . $wishButton . "
                <!-- Segnaposto -->
                <label for='quantita'>Quantit&agrave;</label>
                </inputQuantita>
            </form>";
        } else {
            // admin

            $present = $connessione->get_active_offer_by_isbn($isbn);
            $saleButton = "";
            if($present->is_empty())
            {
                $saleButton = "<form action='applicaSconto.php?isbn=" . $isbn . "' method='post'>
                                    <input type='submit' class='button' value='Applica sconto' />
                               </form>";
            }

            $formBottoni = "
            <form action='aggiungiLibro.php?isbn=" . $isbn . "' method='post'>
                <input type='submit' class='button' name='modificaLibroTrigger' value='Modifica libro' />
            </form>" . $saleButton;

            // se sono admin, vedo quante copie ci sono nel db
            $infoGenerali .= "<p class='miniGrassetto'>Ci sono " . $tmp[0]['quantita'] . " copie in magazzino</p>";

            // se sono admin, non posso lasciare recensioni
            $linkLasciaRecensione = "";
        }


        // Replace

        // il replace </formBottoni> DEVE essere prima di quello </inputQuantita>
        $paginaHTML = str_replace("</formBottoni>", $formBottoni, $paginaHTML);

        // ----
        $paginaHTML = str_replace("</imgLibro>", $imgLibro, $paginaHTML);
        $paginaHTML = str_replace("</infoGenerali>", $infoGenerali, $paginaHTML);
        $paginaHTML = str_replace("</trama>", $trama, $paginaHTML);
        $paginaHTML = str_replace("</dettagliLibro>", $dettagliLibro, $paginaHTML);
        $paginaHTML = str_replace("</generi>", $generi, $paginaHTML);
        $paginaHTML = str_replace("</inputQuantita>", $inputQuantita, $paginaHTML);
        $paginaHTML = str_replace("</listaRecensioni>", $listaRecensioni, $paginaHTML);
        $paginaHTML = str_replace("</linkLasciaRecensione>", $linkLasciaRecensione, $paginaHTML);
    } else {
        $trovatoErrore = true;
    }
    $connessione->closeConnection();
} else {
    $trovatoErrore = true;
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

if ($trovatoErrore) {
    // Errore, pagina senza genereId o con idGenere sbagliato
    header("Location: error.php");
}

// -------------------

echo $paginaHTML;
