<?php
session_start();

use DB\Service;

require_once('backend/db.php');

$username = $_POST["username"];
$password = $_POST["password"];

$connessione = new Service();

$c = $connessione->openConnection();

if (!$c) {
  $_SESSION["error"] = "Impossibile connettersi al sistema.";
  header("Location: accedi.php");
} else {
  $usernameCheck = (isset($username) && preg_match('/^[A-Za-z\s]\w{2,10}$/', $username));
  $passwordCheck = (isset($password) && preg_match('/^[\w~!@#$%^&*--+={}\[\]|\\:;<>,.?_]+.{2,20}$/', $password));
  if ($usernameCheck && $passwordCheck) {

    $log = $connessione->login($username, $password);

    if ($log->ok()) {
      if (!$log->is_empty()) {


        $_SESSION["Codice_identificativo"] = $log->get_result()[0]['codice_identificativo'];
        $_SESSION["Nome"] = $log->get_result()[0]['nome'];
        $_SESSION["Cognome"] = $log->get_result()[0]['cognome'];
        $_SESSION["Data_nascita"] = $log->get_result()[0]['data_nascita'];
        $_SESSION["Username"] = $log->get_result()[0]['username'];
        $_SESSION["Email"] = $log->get_result()[0]['email'];
        $_SESSION["Telefono"] = $log->get_result()[0]['telefono'];
        $connessione->closeConnection(); // chiudo la connessione
        header("Location: index.php");
      } else {
        $_SESSION["info"] = $log->get_error_message();
        $connessione->closeConnection(); // chiudo la connessione
        header("Location: accedi.php");
      }
    } else {
      $_SESSION["info"] = "Non tutti i campi sono stati inseriti";
      $connessione->closeConnection(); // chiudo la connessione
      header("Location: accedi.php");
    }
  } else {
    $connessione->closeConnection(); // chiudo la connessione
    $_SESSION["info"] = $log->get_error_message();
    header("Location: accedi.php");
  }
}
