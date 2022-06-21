<?php

    session_start();

    use DB\Service;

    if(!isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else if(!isset($_POST['username'])||!isset($_POST['email']))
    {
        header("Location: datilogin.php");
    }
    else
    {
        require_once('backend/db.php');
        $connessione = new Service();
        $a = $connessione->openConnection();
        
        $oldUsername = $_SESSION['Username'];
        $oldMail = $_SESSION['Email'];
        $oldPassword = "";

        $oldArray = array(
            "username" => $oldUsername,
            "email" => $oldMail,
        );


        $newUsername = $_POST['username'];
        $newMail = $_POST['email'];

        $newArray = array(
            "username" => $newUsername,
            "email" => $newMail,
        );

        print_r($oldArray);
        print_r($newArray);

        $connessione->update_user_data($_SESSION['Codice_identificativo'],$oldArray,$newArray);
    
        $connessione->closeConnection();

        header("Location:datilogin.php");
    }
?>