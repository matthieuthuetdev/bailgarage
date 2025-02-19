<<?php
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
    header("Location: liste_paiements.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paiement_id'])) {
    $paiement_id = $_POST['paiement_id'];
    $emailSubject = $_POST['emailSubject'];
    $emailContent = $_POST['emailContent'];
    $sendCopy = filter_var($_POST["sendCopy"], FILTER_VALIDATE_BOOLEAN);

    $stmt_info_paiement = $db->prepare("
        SELECT p.paiement_id, p.bail_id, p.montant, l.email, l.nom AS locataire_nom, l.prenom AS locataire_prenom, g.addresse, g.numero_garage
        FROM paiements p
        JOIN baux b ON p.bail_id = b.bail_id
        JOIN locataires l ON b.locataire_id = l.locataire_id
        JOIN garages g ON b.garage_id = g.garage_id
        WHERE p.paiement_id = ? AND g.proprietaire_id = ?
    ");
    $stmt_info_paiement->execute([$paiement_id, $proprietaire_id]);
    $paiement_info = $stmt_info_paiement->fetch(PDO::FETCH_ASSOC);

    if ($paiement_info) {
        $locataire_nom = $paiement_info['locataire_nom'] . ' ' . $paiement_info['locataire_prenom'];
        $locataire_email = $paiement_info['email'];
        $montant = $paiement_info['montant'];
        $addresse_garage = $paiement_info['addresse'] . ' - ' . $paiement_info['numero_garage'];

        $emailContent = str_replace('{locataire_nom}', $locataire_nom, $emailContent);
        $emailContent = str_replace('{addresse_garage}', $addresse_garage, $emailContent);
        $emailContent = str_replace('{montant}', $montant, $emailContent);

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
            $mail->addAddress($locataire_email, $locataire_nom);

            if ($sendCopy) {
                $stmt = $db->prepare("SELECT email FROM proprietaires WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $proprietaire_email = $stmt->fetchColumn();
                $mail->addAddress($proprietaire_email);
            }

            $mail->isHTML(true);
            $mail->Subject = $emailSubject;
            $mail->Body = $emailContent;

            $mail->send();
            $_SESSION['success_message'] = "L'e-mail de relance a été envoyé avec succès à $locataire_email.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Erreur lors de l'envoi de l'e-mail de relance : {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error_message'] = "Aucune information trouvée pour le paiement ID $paiement_id.";
    }

    header("Location: liste_paiements.php");
    exit();
}

header("Location: liste_paiements.php");
exit();
?>
