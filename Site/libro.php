<?php
    use DB\Service;
    require_once('backend/db.php');

    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("libro_php.html");

    // Accesso al database

    $trovatoErrore = false;

    if(isset($_GET['isbn']))
    {
        $isbn = $_GET['isbn'];

        $connessione = new Service();
        $a = $connessione->openConnection();

        $queryIsbn = $connessione->get_book_by_isbn($isbn);

        if(count($queryIsbn) > 0)
        {
            // Ce un libro con quell'isbn, posso andare avanti

            // ---- IMG LIBRO ----
            $imgLibro = "<img id='imgLibro' src=" . $queryIsbn[0]['Percorso'] . ">";

            // ---- INFO GENERALI ----
            $infoGenerali = "<ul>";
            $infoGenerali .= "<li id='titolo'>" . $queryIsbn[0]['Titolo'] ."</li>";
            $infoGenerali .= "<li>" . $queryIsbn[0]['autore_nome'] ." " . $queryIsbn[0]['autore_cognome'] . "</li>";
            $infoGenerali .= "<li>" . $queryIsbn[0]['Prezzo'] ."&euro;</li>";
            $infoGenerali .= "<li>" . $queryIsbn[0]['Quantita'] ." pz.</li>";
            $infoGenerali .= "</ul>";

            // ---- TRAMA ----
            $trama = "<h3>Descrizione</h3>";
            $trama .= "<p>" . $queryIsbn[0]['Trama'] ."</p>";

            // ---- DETTAGLI LIBRO ----
            $dettagliLibro = "<ul>";
            $dettagliLibro .= "<h3>Dettagli Libro</h3>";
            $dettagliLibro .= "<li>Titolo: " . $queryIsbn[0]['Titolo'] . "</li>";
            $dettagliLibro .= "<li>Autore: " . $queryIsbn[0]['autore_nome'] ." " . $queryIsbn[0]['autore_cognome'] . "</li>";
            $dettagliLibro .= "<li>ISBN:" . $queryIsbn[0]['ISBN'] . "</li>";
            $dettagliLibro .= "<li>Editore:" . $queryIsbn[0]['editore_nome'] . "</li>";
            $dettagliLibro .= "<li>Data pubblicazione: " . $queryIsbn[0]['Data_Pubblicazione'] . "</li>";
            $dettagliLibro .= "<li>Numero Pagine: " . $queryIsbn[0]['Pagine'] . "</li>";
            
            // generi
            $queryGeneri = $connessione->get_genres_from_isbn($isbn);
            $generi = "<li>Gener";
            $generi .= (count($queryGeneri) > 1) ? "i: " : "e: ";

            $cont = 0;
            foreach($queryGeneri as $genere)
            {
                $generi .= $genere['Nome'];

                if(++$cont < count($queryGeneri))
                {
                    $generi .= ", ";
                }
            }
            $generi .= "</li>";

            $dettagliLibro .= $generi;

            $dettagliLibro .= "</ul>";

            // ---- QUANTITA ----
            $inputQuantita = "<input type='number' id='quantita' name='quantita' value='1' min='1' step='1' max='" . $queryIsbn[0]['Quantita'] . "'/>";
            


            // Replace
            $paginaHTML = str_replace("</imgLibro>", $imgLibro, $paginaHTML);
            $paginaHTML = str_replace("</infoGenerali>", $infoGenerali, $paginaHTML);
            $paginaHTML = str_replace("</trama>", $trama, $paginaHTML);
            $paginaHTML = str_replace("</dettagliLibro>", $dettagliLibro, $paginaHTML);
            $paginaHTML = str_replace("</generi>", $generi, $paginaHTML);
            $paginaHTML = str_replace("</inputQuantita>", $inputQuantita, $paginaHTML);
        }
        else
        {
            $trovatoErrore = true;
        }

        $connessione->closeConnection();
    }
    else
    {
        $trovatoErrore = true;
    }

    if($trovatoErrore)
    {
        // Errore, pagina senza genereId o con idGenere sbagliato
        $errore = "<img src='images/404.jpg' alt='Errore 404, genere inesistente' id='erroreImg'>";

        $paginaHTML = str_replace("</listaNuovi>", $errore, $paginaHTML);
        $paginaHTML = str_replace("</nomeGenere>", "Errore", $paginaHTML);
    }

    // -------------------

    echo $paginaHTML;
?>