<?php
    use DB\Service;
    require_once('backend/db.php');

    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("index_php.html");

    // Accesso al database
    $connessione = new Service();
    $a = $connessione->openConnection();

    $limit = 7;
    // ---- BESTSELLER ----
    $queryBestseller = $connessione->get_bestsellers();

    $listaBestseller = "<ul class='bookCards'>";
    $cont = 0;
    foreach($queryBestseller as $libro)
    {
        if($cont++ < $limit)
        {
            $listaBestseller .= "<li><a href=''><img class='homeCardsImg' src='" . $libro['Percorso'] ."' alt=''>" . $libro['Titolo'] . "</a></li>";
        }
    }
    $listaBestseller .= "</ul>";

    // ---- NUOVE USCITE ----
    $queryNuovi = $connessione->get_new_books();
    
    $listaNuovi = "<ul class='bookCards'>";
    $cont = 0;
    foreach($queryNuovi as $libro)
    {
        if($cont++ < $limit)
        {
            $listaNuovi .= "<li><a href=''><img class='homeCardsImg' src='" . $libro['Percorso'] ."' alt=''>" . $libro['Titolo'] . "</a></li>";
        }
    }
    $listaNuovi .= "</ul>";

    // ---- SOTTO I 5 EURO ----
    $queryUnder5 = $connessione->get_books_under_5();
    
    $listaUnder5 = "<ul class='bookCards'>";
    $cont = 0;
    foreach($queryUnder5 as $libro)
    {
        if($cont++ < $limit)
        {
            $listaUnder5 .= "<li><a href=''><img class='homeCardsImg' src='" . $libro['Percorso'] ."' alt=''>" . $libro['Titolo'] . "</a></li>";
        }
    }
    $listaUnder5 .= "</ul>";



    $paginaHTML = str_replace("</listaBestseller>", $listaBestseller, $paginaHTML);
    $paginaHTML = str_replace("</listaNuovi>", $listaNuovi, $paginaHTML);
    $paginaHTML = str_replace("</listaUnder5>", $listaUnder5, $paginaHTML);

    $connessione->closeConnection();
    // -------------------

    echo $paginaHTML;
?>