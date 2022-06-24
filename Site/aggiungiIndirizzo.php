<?php
session_start();

require_once "graphics.php";

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    $paginaHTML = graphics::getPage("aggiungiIndirizzo_php.html");
    $paginaHTML = str_replace("</error>", "", $paginaHTML);
    echo $paginaHTML;
}
