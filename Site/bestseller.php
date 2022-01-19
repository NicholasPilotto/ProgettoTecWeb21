<?php
    use DB\Service;
    require_once('backend/db.php');

    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("bestseller_php.html");

    // Accesso al database
    $connessione = new Service();
    $a = $connessione->openConnection();

    $limit = 12;

    $queryBestseller = $connessione->get_bestsellers();

    $listaBestseller = "<ul class='bookCards'>";
    $cont = 0;
    foreach($queryBestseller as $libro)
    {
        if($cont++ < $limit)
        {
            $listaBestseller .= "<li><a href='libro.php?isbn=" . $libro['ISBN'] . "'><img class='generiCardsImg' src='" . $libro['Percorso'] . "' alt=''>" . $libro['Titolo'] . "</a></li>";
        }
    }
    $listaBestseller .= "</ul>";

    $paginaHTML = str_replace("</listaBestseller>", $listaBestseller, $paginaHTML);

    $connessione->closeConnection();
    // -------------------

    echo $paginaHTML;
?>