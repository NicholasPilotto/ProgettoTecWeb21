<?php

session_start();

use DB\Service;

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    require_once('backend/db.php');
    require_once "graphics.php";

    $paginaHTML = graphics::getPage("modificapassword_php.html");

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


    echo $paginaHTML;
}
