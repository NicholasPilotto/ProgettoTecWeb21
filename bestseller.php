<?php
    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("bestseller_php.html");

    // Accesso al database

    // -------------------

    echo $paginaHTML;
?>