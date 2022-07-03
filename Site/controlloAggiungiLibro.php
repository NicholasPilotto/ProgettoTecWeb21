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
$data = $_POST["dataPub"];
$trama = $_POST["trama"];
$categoria = $_POST["categoria"];

$percorso = $_POST["imgSrc"];

$isbnCheck = (isset($isbn) && preg_match('/^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/', $isbn));
$titoloCheck = (isset($titolo) && preg_match('/^[a-zA-Z0-9 <>"=/òàùèéÈÉÀÁÒÓÙÚ()\'?.,!-]{10,500}$/', $titolo));
$copertinaCheck = (isset($copertina));
$autoreCheck = (isset($autore));
$editoreCheck = (isset($editore));
$prezzoCheck = (isset($prezzo) && preg_match('/^([1-9][0-9]*)([.]([0-9]+))*$/', $prezzo));
$pagineCheck = (isset($pagine) && preg_match('/^[1-9][0-9]*$/', $pagine));
$quantitaCheck = (isset($quantita) && preg_match('/^[1-9][0-9]*$/', $quantita));
$dataCheck = (isset($data));
$tramaCheck = (isset($trama) && preg_match('/^[a-zA-Z0-9 =«»åòàùèéÈÉÀÁÒÓÙÚìÌÍ()\'’´?.,!-<>{}[\]]{10,2500}$/', $trama));
$categoriaCheck = (isset($categoria));


if (!$a) {
  $_SESSION["error"] = "Impossibile connettersi al sistema";
  header("Location: aggiungiLibro.php?isbn=" . $isbn);
}

if (isset($_POST["modificaLibroTrigger"])) {
  // modifica libro
  // echo var_dump($isbnCheck) . " " . var_dump($titoloCheck) . " " . var_dump($editoreCheck) . " " . var_dump($pagineCheck) . " " . var_dump($prezzoCheck) . " " . var_dump($quantitaCheck) . " " . var_dump($dataCheck) . " " . var_dump($copertinaCheck) . " " . var_dump($tramaCheck) . " " . !empty($autoreCheck)  . " " . !empty($categoriaCheck);
  // echo $dataPubblicazione;
  // die();

  if ($isbnCheck) {
    $oldBookData = $connessione->get_book_by_isbn($isbn);
    $autoriToChange = array();
    foreach ($autore as $aut) {
      echo $aut;
      array_push($autoriToChange,  $aut);
    }

    $oldCategorie = $connessione->get_genres_from_isbn($isbn);
    $categorieToCahange = array();
    foreach ($categoria as $cat) {
      array_push($categorieToCahange, $cat);
    }

    $auxOld = $oldBookData->get_result()[0];

    $pathToChange = NULL;

    if ($copertina["size"] != 0) {
      if (move_uploaded_file($_FILES["copertina"]["tmp_name"], "./images/books/" . $_FILES["copertina"]["name"])) {
        $pathToChange = "images/books/" . $_FILES["copertina"]["name"];
      } else {
        $_SESSION["info"] = "Impossibile salvare immagine di copertina.";
        header("Location: aggiungiLibro.php?isbn=" . $isbn);
      }
    }

    $titoloToChange = ($titolo != $auxOld["titolo"]) ? $titolo : NULL;
    $editoreToChange = ($editore != $auxOld["editore"]) ? $editore : NULL;
    $pagineToChange = ($pagine != $auxOld["pagine"]) ? $pagine : NULL;
    $quantitaToChange = ($quantita != $auxOld["quantita"]) ? $quantita : NULL;
    $prezzoToChange = ($prezzo != $auxOld["prezzo"]) ? $prezzo : NULL;
    $dataPubToChange = ($data != $auxOld["data_pubblicazione"]) ? $data : NULL;
    $tramaToChange = ($trama != $auxOld["trama"]) ? $trama : NULL;

    $edit = $connessione->edit_book($isbn, $titoloToChange, $editoreToChange, $pagineToChange, $prezzoToChange, $quantita, $dataPubToChange, $pathToChange, $tramaToChange, $autoriToChange, $categorieToCahange);


    if ($edit->ok()) {
      $_SESSION["success"] = "Libro modificato con successo!";
      $connessione->closeConnection();
      header("Location: aggiungiLibro.php?isbn=" . $isbn);
    } else {
      $_SESSION["info"] = $edit->get_error_message();
    }
  } else {
    $_SESSION["info"] = "Dati mancanti o non corretti.";
  }
  $connessione->closeConnection();
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

    if ($isbnCheck && $titoloCheck && $editoreCheck && $pagineCheck && $prezzoCheck && $quantitaCheck && $dataCheck && $copertinaCheck && $tramaCheck && !empty($autoreCheck) && !empty($categoriaCheck)) {
      $aux = $connessione->insert_book($isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data, $path, $autore, $categoria, $trama);
      if ($aux->ok()) {
        $_SESSION["success"] = "Libro inserito correttamente";
      } else {
        $_SESSION["info"] = $aux->get_error_message();
      }
    } else {
      $_SESSION["info"] = "Dati mancanti o non corretti.";
    }
  } else {
    $_SESSION["info"] = "La copertina sembra non essere presente, ricontrolla.";
  }
  $connessione->closeConnection();
}

header("Location: aggiungiLibro.php?isbn=" . $isbn);
