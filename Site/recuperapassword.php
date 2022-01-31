<?php
    session_start();
    if(isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {
        require_once "graphics.php";
        
        $paginaHTML = graphics::getPage("recuperapassword_php.html");

        if(isset($_SESSION["mailError"]))
        {
            $errorMsg = "<h3>" . $_SESSION["mailError"] . "</h3>";
            session_destroy();
        }
        else
        {
            $errorMsg = "";
        }

        $paginaHTML = str_replace("</errorMsg>", $errorMsg, $paginaHTML);

        echo $paginaHTML;
    }
?>