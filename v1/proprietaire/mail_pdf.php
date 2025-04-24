<?php

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/phpmailer/src/Exception.php';
require '../includes/phpmailer/src/PHPMailer.php';
require '../includes/phpmailer/src/SMTP.php';

// Function to send email with attachment
function send_email_with_attachment($to, $subject, $body, $filePath) {
    try {
        $mail = new PHPMailer(true);
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lililizalopi2021@gmail.com'; // Change to your email
        $mail->Password = 'esbb phvk adqu zzgm'; // Change to your email password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        // Sender and recipient
        $mail->setFrom('lililizalopi2021@gmail.com', 'Admin BailGarage'); // Change to your email
        $mail->addAddress($to);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        // Attachment
        $mail->addAttachment($filePath, 'bail_confirmation.pdf'); // Attach PDF
        
        // Send email
        return $mail->send();
        
    } catch (Exception $e) {
        return false;
    }
}

// Fetch bail ID from the GET parameters
$bail_id = $_GET['id'] ?? null;
if (!$bail_id) {
    die("Bail ID manquant.");
}

// Fetch PDF content from the generate_pdf.php script
$generatePdfPath = 'generate_pdf.php';
$pdf_content = @file_get_contents("$generatePdfPath?id=$bail_id");
if ($pdf_content === false) {
    die("Échec de la génération du PDF.");
}

// Save PDF content to a file
$filePath = "../uploads/bail_confirmation_$bail_id.pdf";
if (file_put_contents($filePath, $pdf_content) === false) {
    die("Failed to save the PDF file.");
}

// Email details
$to = $_GET['email'];
$prenom = $_GET['prenom'];
$nom = $_GET['nom'];
$adresse = $_GET['adresse'];
$ville = $_GET['ville'];
$montant_loyer = $_GET['montant_loyer'];

$subject = 'Confirmation du Bail';
$body = "Bonjour " . htmlspecialchars($prenom) . " " . htmlspecialchars($nom) . ",<br><br>Voici les détails de votre bail:<br>"
        . "Adresse: " . htmlspecialchars($adresse) . "<br>"
        . "Ville: " . htmlspecialchars($ville) . "<br>"
        . "Montant du Loyer: " . htmlspecialchars($montant_loyer) . " EUR<br><br>"
        . "Cordialement,<br>L'équipe BailGarage";

// Send email with PDF attachment
if (send_email_with_attachment($to, $subject, $body, $filePath)) {
    echo 'Email sent successfully.';
} else {
    echo 'Failed to send email.';
}
?>
