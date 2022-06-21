<?php
session_start();

if(!isset($_SESSION))
{
    header("Location:index.php");
}


  use DB\Service;

  require_once('backend/db.php');

  $oldPassword = $_POST["vecchiapassword"];
  $newPassword = $_POST["nuovapassword"];
  $username = $_SESSION["Username"];
  $codice = $_SESSION["Codice_identificativo"];

  $connessione = new Service();

  $connessione->openConnection();

  $log = $connessione->login($username, $oldPassword);

  if ($log->ok()) {
    if (!$log->is_empty()) {
      $connessione->change_psw($codice,$newPassword);
      $connessione->closeConnection(); // chiudo la connessione
      header("Location: modificapassword.php");
    }
  } else {
    $connessione->closeConnection(); // chiudo la connessione
    $_SESSION["error"] = $log->get_error_message();
    header("Location: modificapassword.php");
}