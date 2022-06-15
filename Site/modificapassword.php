<?php

    session_start();

    use DB\Service;
    
    if(!isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {
        require_once('backend/db.php');
        require_once "graphics.php";
        
        $paginaHTML = graphics::getPage("modificapassword_php.html");
        

        echo $paginaHTML;
    }
?>