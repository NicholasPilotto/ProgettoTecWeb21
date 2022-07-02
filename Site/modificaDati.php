<?php

session_start();

use DB\Service;

require_once('backend/db.php');

if (!isset($_SESSION["Nome"])) {
    $_SESSION["error"] = "La sessione non sembra essere integra.";
    header("Location: index.php");
    $_SESSION["error"] = "La sessione non sembra essere integra.";
} else {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $usernameCheck = (isset($username) && preg_match('/^[A-Za-z\s]\w{2,10}$/', $username));
    $emailCheck = (isset($email) && preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email));

    if ($usernameCheck && $emailCheck) {

        $connessione = new Service();
        $a = $connessione->openConnection();

        if (!$a) {
            $_SESSION["error"] = "Impossibile stabilire una connessione con il sistema.";
            header("Location: datilogin.php");
        } else {

            $oldUsername = $_SESSION['Username'];
            $oldMail = $_SESSION['Email'];
            $oldUsernameCheck = (isset($oldUsername) && preg_match('/^[A-Za-z\s]\w{2,10}$/', $oldUsername));
            $oldMailCheck = (isset($oldMail) && preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $oldMail));


            if ($oldUsernameCheck && $oldMailCheck) {

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

                    $id = $_SESSION['Codice_identificativo'];

                    if (isset($id)) {

                        $data = $connessione->update_user_data($id, $oldArray, $newArray);

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
                        $_SESSION["error"] = "La sessione sembra corrotta";
                    }
                } else {
                    $_SESSION["info"] = "Non tutti i campi sembrano essere compilati";
                }
            } else {
                $_SESSION["error"] = "La sessione non sembra essere integra";
            }
            $connessione->closeConnection();
        }
    } else {
        $_SESSION["info"] = "Dati mancanti o non corretti.";
    }
    header("Location:datilogin.php");
}
