<?php
session_start();

require_once "graphics.php";

$paginaHTML = graphics::getPage("generi_php.html");

// Accesso al database

// -------------------
$paginaHTML = str_replace('<li class="nav-item"><a href="generi.php">Generi</a></li>', '<li class="nav-item selectedNavItem">Generi</li>', $paginaHTML);
echo $paginaHTML;
