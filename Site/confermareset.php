<?php
session_start();
use DB\Service;
require_once('backend/db.php');

if(isset($_SESSION["mailRecupero"]))
{
    $codice = $_POST["codice"];
    $password = $_POST["password"];

    $connessione = new Service();
    $a = $connessione->openConnection();

    $utente = $connessione->get_utente_by_email($_SESSION["mailRecupero"]);

    if($connessione->is_code_correct($codice,$utente->get_result()[0]["Codice_identificativo"])->ok())
    {
        $connessione->restore_psw($utente->get_result()[0]["Codice_identificativo"],$password,$codice);
        session_destroy();
        header("Location: index.php");
    }
    else
    {
        $_SESSION["erroreCodice"] = "codice incorretto";
        header("Location: resetpassword.php");
        die();
    }
}
else
{
    header("Location: recuperapassword.php");
    die();
}


?>