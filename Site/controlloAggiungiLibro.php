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

if (!$a) {
  $_SESSION["error"] = "Impossibile connettersi al sistema";
  header("Location: aggiungiLibro.php");
}


$isbn = $_POST["isbn"];
$titolo = $_POST["titolo"];
$copertina = $_FILES["copertina"];
$autore = $_POST["autore"];
$editore = $_POST["editore"];
$prezzo = $_POST["prezzo"];
$pagine = $_POST["pagine"];
$quantita = $_POST["quantita"];
$data = $_POST["dataPubblicazione"];
$trama = $_POST["trama"];
$categoria = $_POST["categoria"];

$path = NULL;

$newName = NULL;

if (file_exists("./images/books/" . $_FILES["copertina"]["name"])) {
  $newName = $_FILES["copertina"]["name"] . date("Y-m-d-H-i-s");
}

if (move_uploaded_file($_FILES["copertina"]["tmp_name"], "./images/books/" . (isset($newName) ? $newName : $_FILES["copertina"]["name"]))) {
  $path = "images/books/" . $_FILES["copertina"]["name"];
} else {
  $_SESSION["info"] = "Impossibile salvare immagine di copertina.";
  header("Location: aggiungiLibro.php");
}

// echo $isbn . " " . $titolo . " " . $editore . " " . $pagine . " " . $prezzo . " " . $quantita . " " . $data . " " . $path . " " . $trama . " " . $autore[0] . " " . $categoria[0];
// die();

if (isset($isbn) && isset($titolo) && isset($editore) && isset($pagine) && isset($prezzo) && isset($quantita) && isset($data) && isset($path) && isset($trama) && !empty($autore) && !empty($categoria)) {
  $aux = $connessione->insert_book($isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data, $path, $autore, $categoria, $trama);
  if ($aux->get_errno() == 0) {
    $_SESSION["success"] = "Libro inserito correttamente";
  } else {
    $_SESSION["info"] = $aux->get_error_message();
  }
} else {
  $_SESSION["info"] = "Non tutti i dati sono stati inseriti";
}
header("Location: aggiungiLibro.php");
