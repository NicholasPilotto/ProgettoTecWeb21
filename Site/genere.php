<?php
    use DB\Service;
    require_once('backend/db.php');

    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("genere_php.html");

    // Accesso al database

    $trovatoErrore = false;

    if(isset($_GET['genere']))
    {
        $idGenere = $_GET['genere'];

        $connessione = new Service();
        $a = $connessione->openConnection();

        $queryNomeGenere = $connessione->get_genre_by_id($idGenere);
        if(count($queryNomeGenere) > 0)
        {
            // Ce un genere con quell'id, posso andare avanti
            $nomeGenere = $queryNomeGenere[0]['Nome'];
            $libri = $connessione->get_books_by_genre($idGenere);

            $listaNuovi = "<ul class='bookCards'>";

            foreach($libri as $libro)
            {
                $listaNuovi .= "<li><a href=''><img class='generiCardsImg' src='" . $libro['Percorso'] ."' alt=''>" . $libro['Titolo'] . "</a></li>";
            }

            $listaNuovi .= "</ul>";

            $paginaHTML = str_replace("</listaNuovi>", $listaNuovi, $paginaHTML);
            $paginaHTML = str_replace("</nomeGenere>", $nomeGenere, $paginaHTML);
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