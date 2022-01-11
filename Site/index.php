<?php
require_once "./Backend/Pages/graphics.php";


$paginaHTML = graphics::getPage("Views/index_php.html");

// Accesso al database

// -------------------

echo $paginaHTML;
