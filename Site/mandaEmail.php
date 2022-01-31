<?php 
session_start();
// Import PHPMailer classes into the global namespace 
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception; 
 
require 'PHPMailer/src/Exception.php'; 
require 'PHPMailer/src/PHPMailer.php'; 
require 'PHPMailer/src/SMTP.php'; 

use DB\Service;
require_once('backend/db.php');

$connessione = new Service();
$a = $connessione->openConnection();

$queryUtente = $connessione->get_utente_by_email($_POST["email"]);

if($queryUtente->get_result() != null)
{
    $mail = new PHPMailer(true); 
    
    $mail->isSMTP();                      // Set mailer to use SMTP 
    $mail->Host = 'smtp.gmail.com';       // Specify main and backup SMTP servers 
    $mail->SMTPAuth = true;               // Enable SMTP authentication 
    $mail->Username = 'secondreadmailer@gmail.com';   // SMTP username 
    $mail->Password = 'Secondread!1';   // SMTP password 
    $mail->SMTPSecure = 'tls';            // Enable TLS encryption, `ssl` also accepted 
    $mail->Port = 587;                    // TCP port to connect to 

    
    // Sender info 
    $mail->setFrom('secondreadmailer@gmail.com', 'Mailer'); 
    
    // Add a recipient 
    $mail->addCC($_POST["email"]); 
    
    //$mail->addCC('cc@example.com'); 
    //$mail->addBCC('bcc@example.com'); 
    
    // Set email format to HTML 
    $mail->isHTML(true); 
    
    // Mail subject 
    $mail->Subject = 'Recupero password SecondRead'; 
    
    // Mail body content 
    $code = uniqid();
    $insertRecupero = $connessione->restore_code($code, $queryUtente->get_result()[0]["Codice_identificativo"]);
    $connessione->closeConnection();
    $bodyContent = '<h1>Recupera password SecondRead</h1>'; 
    $bodyContent .= '<p>Questa email è stata generata automaticamente. Se non hai richiesto un cambiamento di password, ignora questa e-mail. Se lo hai richiesto, inserisci questo codice:</p>'; 
    $bodyContent .= '<h1>' . $code . '</h1>';
    $mail->Body    = $bodyContent; 
    
    // Send email 
    if(!$mail->send()) { 
        $_SESSION["mailError"] = "Non è stato possibile inviare la mail.";
        header("Location: recuperapassword.php");
        die();
    }
    $_SESSION["mailRecupero"] = $queryUtente->get_result()[0]["Email"];
    header("Location: resetpassword.php");
    die(); 
}
else
{
    $_SESSION["mailError"] = "L'email non risulta presente nei nostri archivi.";
    $connessione->closeConnection();
    header("Location: recuperapassword.php");
    die();
}
 
?>