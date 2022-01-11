<?php
    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("accedi_php.html");

    // Accesso al database

    // -------------------

    echo $paginaHTML;
?>