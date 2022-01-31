<?php
    session_start();
    if(isset($_SESSION["mailRecupero"]))
    {
        if(isset($_SESSION["Nome"]))
        {
            header("Location: index.php");
        }
        else
        {    
            if(isset($_SESSION["errorCodice"]))
        {
            $errorMsg = "<h3>" . $_SESSION["errorCodice"] . "</h3>";
        }
        else
        {
            $errorMsg = "";
        }
            require_once "graphics.php";

            $paginaHTML = graphics::getPage("resetpassword_php.html");

            // Accesso al database

            // -------------------

            $paginaHTML = str_replace("</errorMsg>", $errorMsg, $paginaHTML);

            echo $paginaHTML;
        }
    }
    else
    {
        header("Location: recuperapassword.php");
        die();
    }
?>