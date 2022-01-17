<?php
    use DB\Service;
    require_once('backend/db.php');

    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("genere_php.html");

    // Accesso al database

    $idGenere = $_GET['genere'];

    if(isset($idGenere))
    {
        $connessione = new Service();
        $a = $connessione->openConnection();

        $nomeGenere = $connessione->get_genre_by_id($idGenere)[0]['Nome'];
        $libri = $connessione->get_new_books_by_genre($idGenere);

        $listaNuovi = "";

        foreach($libri as $libro)
        {
            $listaNuovi .= "<li><a href=''><img class='generiCardsImg' src='" . $libro['Percorso'] ."' alt=''>" . $libro['Titolo'] . "</a></li>";
        }

        $paginaHTML = str_replace("</listaNuovi>", $listaNuovi, $paginaHTML);
        $paginaHTML = str_replace("</nomeGenere>", $nomeGenere, $paginaHTML);

        $connessione->closeConnection();
    }
    else
    {

    }

    // -------------------

    echo $paginaHTML;
?>