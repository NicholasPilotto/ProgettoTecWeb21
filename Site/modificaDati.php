<?php

    session_start();

    use DB\Service;

    if(!isset($_SESSION["Nome"])||!isset($_POST['username'])||!isset($_POST['username'])||!isset($_POST['password']))
    {
        header("Location: index.php");
    }
    else
    {
        require_once('backend/db.php');
        $connessione = new Service();
        $a = $connessione->openConnection();

        //print_r($_SESSION);
        
        $oldUsername = $_SESSION['Username'];
        $oldMail = $_SESSION['Email'];
        $oldPassword = "";

        $oldArray = array(
            "username" => $oldUsername,
            "email" => $oldMail,
        );

        print_r($oldArray);

        $newUsername = $_POST['username'];
        $newMail = $_POST['email'];

        $newArray = array(
            "username" => $newUsername,
            "email" => $newMail,
        );

        $connessione->update_user_data($_SESSION['Codice_identificativo'],$oldArray,$newArray);
    
        $connessione->closeConnection();
    }
?>