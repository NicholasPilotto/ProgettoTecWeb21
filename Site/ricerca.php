<?php
    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("ricerca_php.html");

    // Accesso al database

    // -------------------

    echo $paginaHTML;
?>