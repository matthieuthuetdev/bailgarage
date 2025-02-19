<?php
session_start();
include_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../proprietaire/connexion_proprietaire.php");
    exit();
}

// Récupérer le contenu modifié de l'email, l'objet et le nom du modèle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailContent']) && isset($_POST['emailSubject']) && isset($_POST['template_name'])) {
    $emailContent = $_POST['emailContent'];
    $emailSubject = $_POST['emailSubject'];
    $template_name = $_POST['template_name'];

    // Mettre à jour ou insérer le contenu de l'email et l'objet dans la base de données
    $stmt = $db->prepare("SELECT COUNT(*) FROM email_templates WHERE template_name = ?");
    $stmt->execute([$template_name]);
    $exists = $stmt->fetchColumn();
    
    if ($exists) {
        // Mettre à jour l'entrée existante
        $stmt = $db->prepare("UPDATE email_templates SET subject = ?, content = ? WHERE template_name = ?");
        $stmt->execute([$emailSubject, $emailContent, $template_name]);
    } else {
        // Insérer une nouvelle entrée
        $stmt = $db->prepare("INSERT INTO email_templates (template_name, subject, content) VALUES (?, ?, ?)");
        $stmt->execute([$template_name, $emailSubject, $emailContent]);
    }

    // Vérifiez si l'exécution a réussi
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Le modèle d'e-mail a été mis à jour avec succès.";
    } else {
        error_log("Aucune ligne mise à jour ou insérée. Email Content: " . $emailContent . ", Template Name: " . $template_name);
        $_SESSION['error_message'] = "Erreur: Le modèle d'e-mail n'a pas été mis à jour.";
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    $_SESSION['error_message'] = "Erreur lors de la mise à jour du modèle d'e-mail.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
