<?php
require_once "graphics.php";

$paginaHTML = graphics::getPage("views/index_php.html", true);

// Accesso al database

// -------------------

echo $paginaHTML;
