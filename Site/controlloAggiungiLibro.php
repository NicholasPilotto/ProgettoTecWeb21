<?php

session_start();

if (!isset($_SESSION["Nome"])) {
  header("Location:index.php");
}

use DB\Service;

require_once('backend/db.php');

require_once "graphics.php";

require_once "cart.php";

$connessione = new Service();
$a = $connessione->openConnection();


$isbn = $_POST["isbn"];
$titolo = $_POST["titolo"];
$copertina = $_FILES["copertina"];
$autore = $_POST["autore"];
$editore = $_POST["editore"];
$prezzo = $_POST["prezzo"];
$pagine = $_POST["pagine"];
$quantita = $_POST["quantita"];
$data = $_POST["data"];
$trama = $_POST["trama"];
$categoria = $_POST["categoria"];


if (move_uploaded_file($_FILES["copertina"]["tmp_name"], "./images/books/" . $_FILES["copertina"]["name"])) {
  echo "ok";
} else {
  echo "errore";
}
