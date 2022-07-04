<?php
session_start();
if (isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    require_once "graphics.php";

    $paginaHTML = graphics::getPage("accedi_php.html");

    if (isset($_SESSION["error"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("error", $_SESSION["error"]), $paginaHTML);
        unset($_SESSION["error"]);
    }
    if (isset($_SESSION["info"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("info", $_SESSION["info"]), $paginaHTML);
        unset($_SESSION["info"]);
    }
    if (isset($_SESSION["success"])) {
        $paginaHTML = str_replace("</alert>", graphics::createAlert("success", $_SESSION["success"]), $paginaHTML);
        unset($_SESSION["success"]);
    } else {
        $paginaHTML = str_replace("</alert>", "", $paginaHTML);
    }

    $paginaHTML = str_replace('<li class="nav-item"><a class="nav-link" href="accedi.php">Area riservata</a></li>', '<li class="nav-item selectedNavItem">Area riservata</li>', $paginaHTML);

    echo $paginaHTML;
}
