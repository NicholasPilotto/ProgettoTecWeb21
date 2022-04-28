<?php
    session_start();

    use DB\Service;
    require_once('backend/db.php');
    require_once "graphics.php";

    if(!isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {
        $paginaHTML = graphics::getPage("lasciaRecensione_php.html");

        // Accesso al database
        
        $trovatoErrore = false;
        if (isset($_GET['isbn']))
        {
            $isbn = $_GET['isbn'];

            // replace della breadcrumb
            $linkDettaglioLibro = "<a href='libro.php?isbn=" . $isbn . "'>Dettagli Libro</a>";
            $paginaHTML = str_replace("</linkDettaglioLibro>", $linkDettaglioLibro, $paginaHTML);
            // ------------------------
        
            $connessione = new Service();
            $a = $connessione->openConnection();
        
            $queryIsbn = $connessione->get_book_by_isbn($isbn);

            if ($queryIsbn->ok() && !$queryIsbn->is_empty())
            {
                $tmp = $queryIsbn->get_result();

                // il libro c'è, ora controllo se c'è già una recensione (caso nel quale si voglia modificarla)
                
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

        if ($trovatoErrore)
        {
            // Errore, pagina senza genereId o con idGenere sbagliato
            header("Location: error.php");
        }
        // -------------------

        echo $paginaHTML;
    }
?>