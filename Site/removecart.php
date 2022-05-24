<?php
if(!isset($_SESSION))
{
  session_start();
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$isbn = $_GET['isbn'];
$c = cart::build_cart_from_session();
$quant = 1;
$c->remove($isbn,$quant);
$c->save();
if($c->get_quantity()==0)
{
    unset($_SESSION["cart"]);
}
header("Location:carrello.php");

?>