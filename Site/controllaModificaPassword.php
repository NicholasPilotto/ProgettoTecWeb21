<?php
session_start();

if (!isset($_SESSION)) {
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
    $aux = $connessione->change_psw($codice, $newPassword);
    if ($aux->ok()) {
      if (!$aux->is_empty()) {
        $_SESSION["success"] = "Modifiche eseguite correttamente";
        header("Location: modificapassword.php");
        $connessione->closeConnection(); // chiudo la connessione
      } else {
        $connessione->closeConnection(); // chiudo la connessione
        $_SESSION["info"] = $log->get_error_message();
        header("Location: modificapassword.php");
      }
    }
  } else {
    $connessione->closeConnection(); // chiudo la connessione
    $_SESSION["info"] = $log->get_error_message();
    header("Location: modificapassword.php");
  }
} else if ($log->get_errno() != 0 || isset($log->get_error_message_mysqli())) {
  echo $log->get_error_message();
  die();
  $connessione->closeConnection(); // chiudo la connessione
  $_SESSION["error"] = "Non è stato possibile collegarsi al sistema.";
  header("Location: modificapassword.php");
}
