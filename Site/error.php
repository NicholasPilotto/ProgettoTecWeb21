<?php
session_start();

use DB\Service;

require_once "graphics.php";

$paginaHTML = graphics::getPage("error_php.html");

$errore = "<img src='images/404.jpg' alt='Errore 404' id='erroreImg'>";

$paginaHTML = str_replace("</errore>", $errore, $paginaHTML);

echo $paginaHTML;
