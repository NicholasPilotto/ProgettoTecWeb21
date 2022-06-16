<?php
    session_start();
    if(isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {
        require_once "graphics.php";
        
        $paginaHTML = graphics::getPage("accedi_php.html");
        
        $paginaHTML = str_replace('<li class="nav-item"><a class="nav-link" href="accedi.php">Area riservata</a></li>', '<li class="nav-item">Area riservata</li>', $paginaHTML);

        // Accesso al database

        // -------------------

        echo $paginaHTML;
    }
