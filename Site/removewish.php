<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['Nome'])) {
    header("Location:accedi.php");
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

$isbn = $_SESSION['wishtoremove'];
if (isset($_SESSION["wishtoremove"])) {
    unset($_SESSION["wishtoremove"]);
    $connessione = new Service();
    $a = $connessione->openConnection();
    $removeQuery = $connessione->remove_from_wishlist($_SESSION["Codice_identificativo"], $isbn);
    header("Location:wishlist.php");
} else {
    header("Location:index.php");
}
