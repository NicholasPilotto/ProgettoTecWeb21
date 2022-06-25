<?php
session_start();
if (isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    require_once "graphics.php";

    $paginaHTML = graphics::getPage("accedi_php.html");

    if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert info '><i class='fa fa-exclamation-triangle'></i> " . $_SESSION["info"] . "</span>", $paginaHTML);
        unset($_SESSION["info"]);
    } else if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</alert>", "<span class='alert error '><i class='fa fa-close'></i> " . $_SESSION["error"] . "</span>", $paginaHTML);
        unset($_SESSION["error"]);
    }

    $paginaHTML = str_replace('<li class="nav-item"><a class="nav-link" href="accedi.php">Area riservata</a></li>', '<li class="nav-item selectedNavItem">Area riservata</li>', $paginaHTML);

    // Accesso al database

    // -------------------

    echo $paginaHTML;
}
