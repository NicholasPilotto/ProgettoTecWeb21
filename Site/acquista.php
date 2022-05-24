<?php
session_start();

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$paginaHTML = graphics::getPage("acquista_php.html");

if(!isset($_SESSION["Nome"])){
    header("Location:accedi.php");
}

// Accesso al database
$carrelloDiv = "";

if(isset($_SESSION["cart"]))
{
    $c = cart::build_cart_from_session();
    $tot = "<div class='carrelloStatus'><p>Costo totale ordine: " . $c->get_total() . "</p></div>";
    $paginaHTML = str_replace("</totale>",$tot, $paginaHTML);
}
else
{
    header("Location:carrello.php");
}
// -------------------
echo $paginaHTML;

?>