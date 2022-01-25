<?php
    session_start();
    if(isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {    
        if(isset($_SESSION["error"]))
    {
        $errorMsg = $_SESSION["error"];
        session_destroy();
    }
    else
    {
        $errorMsg = "";
    }
        require_once "graphics.php";

        $paginaHTML = graphics::getPage("registrati_php.html");

        // Accesso al database

        // -------------------

        $paginaHTML = str_replace("</errorMsg>", $errorMsg, $paginaHTML);

        echo $paginaHTML;
    }
?>