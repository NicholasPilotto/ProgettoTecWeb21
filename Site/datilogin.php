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
        
        $paginaHTML = graphics::getPage("datilogin_php.html");

        // Accesso al database

        // -------------------

        $connessione = new Service();
        $a = $connessione->openConnection();

        $utente = $connessione->get_utente_by_id($_SESSION["Codice_identificativo"]);

        if ($utente->ok()) {
            $paginaHTML = str_replace("</nameCurrent>", $utente->get_result()[0]['nome'], $paginaHTML);
            $paginaHTML = str_replace("</surnameCurrent>", $utente->get_result()[0]['cognome'], $paginaHTML);
            $paginaHTML = str_replace("</emailCurrent>", $utente->get_result()[0]['email'], $paginaHTML);
            $paginaHTML = str_replace("</usernameCurrent>", $utente->get_result()[0]['username'], $paginaHTML);
            $paginaHTML = str_replace("</psw>", "", $paginaHTML);
        }
        

        echo $paginaHTML;
    }
?>