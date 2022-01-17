<?php
    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("generi_php.html");

    // Accesso al database
    
    // -------------------

    echo $paginaHTML;
?>