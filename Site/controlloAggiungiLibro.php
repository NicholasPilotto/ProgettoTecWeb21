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

$percorso = $_POST["imgSrc"];


if (isset($_SESSION["editFlag"])) {
  // modifica libro

  // echo var_dump(isset($isbn)) . "1 " . var_dump(isset($titolo)) . "2 " . var_dump(isset($editore)) . "3 " . var_dump(isset($pagine)) . "4 " . var_dump(isset($prezzo)) . "5 " . var_dump(isset($quantita)) . "6 " . var_dump(isset($data)) . "7 " . var_dump(isset($copertina)) . "8 " . var_dump(isset($trama)) . "9 " . var_dump(!empty($autore)) . "10 " . var_dump(!empty($categoria));
  // die();
  if (isset($isbn) && isset($titolo) && isset($editore) && isset($pagine) && isset($prezzo) && isset($quantita) && isset($data) && isset($copertina) && isset($trama) && !empty($autore) && !empty($categoria)) {
    $oldBookData = $connessione->get_book_by_isbn($isbn);
    $autors = array();
    foreach ($oldBookData->get_result() as $libro) {
      array_push($autors,  $libro['autore_id']);
    }
    $oldCategorie = $connessione->get_genres_from_isbn($isbn);
    $categories = array();
    foreach ($oldCategorie->get_result() as $cat) {
      array_push($categories, $cat['codice_categoria']);
    }


    $autoriToChange = array_diff($autore, $autors);
    $categorieToCahange = array_diff($categoria, $categories);


    $auxOld = $oldBookData->get_result()[0];

    $pathToChange = NULL;
    $newName = NULL;

    // echo $auxOld["percorso"] . " " . $percorso;
    // die();

    if ($auxOld["percorso"] != $percorso) {
      if (file_exists("./images/books/" . $_FILES["copertina"]["name"])) {
        $newName = $_FILES["copertina"]["name"];
      }
      if (move_uploaded_file($_FILES["copertina"]["tmp_name"], "./images/books/" . (isset($newName) ? $newName : $_FILES["copertina"]["name"]))) {
        $pathToChange = "images/books/" . $_FILES["copertina"]["name"];
      } else {
        $_SESSION["info"] = "Impossibile salvare immagine di copertina.";
        header("Location: aggiungiLibro.php?isbn=" . $isbn);
      }
    }

    // echo $isbn . " " . $titolo . " " . $editore . " " . $pagine . " " . $prezzo . " " . $quantita . " " . $data . " " . $copertina . " " . $trama . " " . $autore . " " . $categoria;
    // die();


    $titoloToChange = ($titolo != $auxOld["titolo"]) ? $titolo : NULL;
    $editoreToChange = ($titolo != $auxOld["editore"]) ? $editore : NULL;
    $pagineToChange = ($pagine != $auxOld["pagine"]) ? $pagine : NULL;
    $quantitaToChange = ($quantita != $auxOld["quantita"]) ? $quantita : NULL;
    $prezzoToChange = ($prezzo != $auxOld["prezzo"]) ? $prezzo : NULL;
    $dataPubToChange = ($data != $auxOld["data_pubblicazione"]) ? $data : NULL;
    $tramaPubToChange = ($trama != $auxOld["trama"]) ? $trama : NULL;

    $edit = $connessione->edit_book($isbn, $titoloToChange, $editoreToChange, $pagineToChange, $prezzoToChange, $quantita, $dataPubToChange, $pathToChange, $autoriToChange, $categorieToCahange);


    if ($edit->ok()) {
      $_SESSION["success"] = "Libro modificato con successo!";
      $connessione->closeConnection();
      header("Location: aggiungiLibro.php?isbn=" . $isbn);
    } else {
      $_SESSION["info"] = $edit->get_error_message();
    }
  }
  $connessione->closeConnection();
  header("Location: aggiungiLibro.php?isbn=" . $isbn);
} else {
  $path = NULL;

  $newName = NULL;

  if (isset($copertina)) {
    if (file_exists("./images/books/" . $_FILES["copertina"]["name"])) {
      $newName = $_FILES["copertina"]["name"] . $isbn;
    }

    if (move_uploaded_file($_FILES["copertina"]["tmp_name"], "./images/books/" . (isset($newName) ? $newName : $_FILES["copertina"]["name"]))) {
      $path = "images/books/" . $_FILES["copertina"]["name"];
    } else {
      $_SESSION["info"] = "Impossibile salvare immagine di copertina.";
      header("Location: aggiungiLibro.php?isbn=" . $isbn);
    }

    if (isset($isbn) && isset($titolo) && isset($editore) && isset($pagine) && isset($prezzo) && isset($quantita) && isset($data) && isset($path) && isset($trama) && !empty($autore) && !empty($categoria)) {
      $aux = $connessione->insert_book($isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data, $path, $autore, $categoria, $trama);
      if ($aux->ok()) {
        $_SESSION["success"] = "Libro inserito correttamente";
      } else {
        $_SESSION["info"] = $aux->get_error_message();
      }
    } else {
      $_SESSION["info"] = "Non tutti i dati sono stati inseriti";
    }
  } else {
    $_SESSION["info"] = "La copertina sembra non essere presente, ricontrolla.";
  }
}

$connessione->closeConnection();
header("Location: aggiungiLibro.php?isbn=" . $isbn);
