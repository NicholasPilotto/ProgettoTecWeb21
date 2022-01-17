<?php
    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("index_php.html");

    // Accesso al database

    // -------------------

    echo $paginaHTML;
?>