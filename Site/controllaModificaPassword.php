<?php
session_start();

use DB\Service;

require_once('backend/db.php');

if (isset($_SESSION["Nome"])) {
  $oldPassword = $_POST["vecchiaPassword"];
  $newPassword = $_POST["nuovaPassword"];


  $oldPasswordCheck = (isset($oldPassword) && preg_match('/^[\w@$.,-;:<>!%*?&_=]{1,16}$/', $oldPassword));
  $newPasswordCheck = (isset($newPassword) && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$.,-;:<>!%*?&_=])[A-Za-z\d@$.,-;:<>!%*?&_=]{8,16}$/', $newPassword));

  $username = $_SESSION["Username"];
  $codice = $_SESSION["Codice_identificativo"];

  $usernameCheck = (isset($username) && preg_match('/^[A-Za-z\s]\w{2,10}$/', $username));
  $idCheck = isset($codice);

  if ($usernameCheck && $idCheck) {
    if ($oldPasswordCheck && $newPasswordCheck) {
      $connessione = new Service();
      $a = $connessione->openConnection();
      if ($a) {
        if ($oldPasswordCheck) {
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
                } else {
                  $_SESSION["info"] = $log->get_error_message();
                }
              } else {
                $_SESSION["error"] = "La sessione non sembra essere integra.";
              }
            } else {
              $_SESSION["info"] = $log->get_error_message();
            }
          } else if ($log->get_errno() != 0) {
            $_SESSION["error"] = "Non è stato possibile collegarsi al sistema.";
          }
        } else {
          $_SESSION["error"] = "La sessione non sembra essere integra.";
        }
        $connessione->closeConnection(); // chiudo la connessione
      } else {
        $_SESSION["error"] = "Impossibile connettersi al sistema.";
      }
    } else {
      $_SESSION["info"] = "Non tutti i campi sono stati completati.";
    }
  } else {
    $_SESSION["error"] = "La sessione sembra essere corrotta.";
  }
  header("Location: modificapassword.php");
} else {
  $_SESSION["error"] = "La sessione sembra essere corrotta.";
  header("Location: modificapassword.php");
}
