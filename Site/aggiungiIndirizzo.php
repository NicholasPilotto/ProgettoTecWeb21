<?php
    session_start();
    if(!isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {
        require_once "graphics.php";
        
        $paginaHTML = graphics::getPage("aggiungiIndirizzo_php.html");

        // Accesso al database

        // -------------------

        echo $paginaHTML;
    }
?>