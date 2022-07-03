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

$isbn = $_REQUEST['wishtoremove'];
if (isset($isbn)) {
    $connessione = new Service();
    $a = $connessione->openConnection();
    if ($a) {
        $removeQuery = $connessione->remove_from_wishlist($_SESSION["Codice_identificativo"], $isbn);
        if ($removeQuery->ok()) {
            $_SESSION["success"] = "Libro rimosso correttamente.";
        } else {
            $_SESSION["info"] = $removeQuery->get_error_message();
        }
    } else {
        $_SESSION["error"] = "Impossibile connettersi al sistema";
    }
    header("Location:wishlist.php");
} else {
    header("Location:index.php");
}
