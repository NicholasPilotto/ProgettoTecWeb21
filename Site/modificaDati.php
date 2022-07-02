<?php

session_start();

use DB\Service;

if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else if (!isset($_POST['username']) || !isset($_POST['email'])) {
    header("Location: datilogin.php");
} else {
    require_once('backend/db.php');
    $connessione = new Service();
    $a = $connessione->openConnection();

    if (!$a) {
        $_SESSION["error"] = "Impossibile stabilire una connessione con il sistema.";
        header("Location: datilogin.php");
    } else {

        $oldUsername = $_SESSION['Username'];
        $oldMail = $_SESSION['Email'];
        // $oldPassword = "";

        if (isset($oldUsername) && isset($oldMail)) {

            $oldArray = array(
                "username" => $oldUsername,
                "email" => $oldMail,
            );

            $newUsername = $_POST['username'];
            $newMail = $_POST['email'];

            if (isset($newUsername) && isset($newMail)) {

                $newArray = array(
                    "username" => $newUsername,
                    "email" => $newMail,
                );

                $data = $connessione->update_user_data($_SESSION['Codice_identificativo'], $oldArray, $newArray);

                if (!$data->ok()) {
                    $_SESSION["info"] = $data->get_error_message();
                } else if ($data->get_error_message_mysqli() != "") {
                    $_SESSION["error"] = "Impossibile stabilire una connessione con il sistema.";
                } else if ($data->get_errno() == 0) {
                    $_SESSION["Username"] = $newUsername;
                    $_SESSION["Email"] = $newMail;
                    $_SESSION["success"] = "Modifica avvenuta correttamente";
                }
            } else {
                $_SESSION["info"] = "Non tutti i campi sembrano essere compilati";
            }

            $connessione->closeConnection();
        } else {
            $_SESSION["error"] = "La sessione non sembra essere integra";
        }
        header("Location:datilogin.php");
    }
}
