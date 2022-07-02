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

if (isset($username) && isset($codice)) {
  if (isset($oldPassword) && isset($newPassword)) {
    $connessione = new Service();
    $a = $connessione->openConnection();
    if ($a) {
      if (isset($oldPassword)) {
        $log = $connessione->login($username, $oldPassword);

        if ($log->ok()) {
          if (!$log->is_empty()) {
            if (isset($codice)) {
              $aux = $connessione->change_psw($codice, $newPassword);
              if ($aux->ok()) {
                if (!$aux->is_empty()) {
                  $_SESSION["success"] = "Modifiche eseguite correttamente";
                } else {
                  $_SESSION["info"] = $log->get_error_message();
                }
              }
            } else {
              $_SESSION["error"] = "La sessione non sembra essere integra.";
              header("Location: modificapassword.php");
            }
            $connessione->closeConnection(); // chiudo la connessione
            header("Location: modificapassword.php");
          } else {
            $connessione->closeConnection(); // chiudo la connessione
            $_SESSION["info"] = $log->get_error_message();
            header("Location: modificapassword.php");
          }
        } else if ($log->get_errno() != 0 || isset($log->get_error_message_mysqli())) {
          $connessione->closeConnection(); // chiudo la connessione
          $_SESSION["error"] = "Non è stato possibile collegarsi al sistema.";
          header("Location: modificapassword.php");
        }
      } else {
        $_SESSION["error"] = "La sessione non sembra essere integra.";
        header("Location: modificapassword.php");
      }
    } else {
      $_SESSION["error"] = "Impossibile connettersi al sistema.";
      header("Location: modificapassword.php");
    }
  } else {
    $_SESSION["info"] = "Non tutti i campi sono stati completati.";
    header("Location: modificapassword.php");
  }
} else {
  $_SESSION["error"] = "La sessione non sembra essere integra.";
  header("Location: modificapassword.php");
}
