<?php
require_once "graphics.php";

$paginaHTML = graphics::getPage("Views/index_php.html");

// Accesso al database

// -------------------

echo $paginaHTML;
