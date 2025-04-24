<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';
require '../includes/phpmailer/src/Exception.php';
require '../includes/phpmailer/src/PHPMailer.php';
require '../includes/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: ../proprietaire/connexion_proprietaire.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_id = $stmt->fetchColumn();

if (!$proprietaire_id) {
    $_SESSION['error_message'] = "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
    header("Location: ajout_locataire.php");
    exit();
}

$stmt = $db->prepare("SELECT email FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_email = $stmt->fetchColumn();

if (isset($_POST["action"]) && $_POST["action"] == "send_email") {
    $subject = $_POST["emailSubject"];
    $content = $_POST["emailContent"];
    $email = $_POST["email"];
    $sendCopy = filter_var($_POST["sendCopy"], FILTER_VALIDATE_BOOLEAN);
	
	 // Remplacer les variables dans le contenu de l'email
    $content = str_replace('{prenom_locataire}', htmlspecialchars($_POST['prenom_locataire']), $content);
    $content = str_replace('{nom_locataire}', htmlspecialchars($_POST['nom_locataire']), $content);
    $content = str_replace('{adresse_garage}', htmlspecialchars($_POST['adresse_garage']), $content);
    $content = str_replace('{proprietaire_mail}', htmlspecialchars($_POST['proprietaire_mail']), $content);
    $content = str_replace('{date_debut}', htmlspecialchars($_POST['date_debut']), $content);
	$content = str_replace('{bail_id}', htmlspecialchars($_POST['bail_id']), $content);


    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lililizalopi2021@gmail.com';
        $mail->Password = 'esbb phvk adqu zzgm';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('lililizalopi2021@gmail.com', 'Admin BailGarage');
        $mail->addAddress($email);

        if ($sendCopy) {
            $mail->addAddress($proprietaire_email);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->send();

        $_SESSION['success_message'] = "Email envoyé avec succès à l'adresse $email.";
        if ($sendCopy) {
            $_SESSION['success_message'] .= "\n Une copie a également été envoyée à vous-même.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Erreur lors de l'envoi de l'email: " . $mail->ErrorInfo;
    }

    header("Location: ajout_locataire.php");
    exit();
}
?>
