<?php
session_start();

require_once "graphics.php";

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    $paginaHTML = graphics::getPage("aggiungiIndirizzo_php.html");
    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert error'><i class='fa fa-times' aria-hidden='true'></i> " . $_SESSION["error"] . "</span>", $paginaHTML);
        unset($_SESSION["error"]);
    } else if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert info'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> " . $_SESSION["error"] . "</span>", $paginaHTML);
        unset($_SESSION["info"]);
    } else if (isset($_SESSION["success"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert success'><i class='fa fa-check' aria-hidden='true'></i> " . $_SESSION["success"] . "</span>", $paginaHTML);
        unset($_SESSION["success"]);
    } else {

        $paginaHTML = str_replace("</alert>", "", $paginaHTML);
    }

    echo $paginaHTML;
}
