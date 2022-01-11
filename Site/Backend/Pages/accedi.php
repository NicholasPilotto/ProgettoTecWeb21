<?php
require_once "graphics.php";

$paginaHTML = graphics::getPage("./Views/accedi_php.html");

// Accesso al database

// -------------------

echo $paginaHTML;
