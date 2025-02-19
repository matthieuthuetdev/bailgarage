<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: ../proprietaire/connexion_proprietaire.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_id = $stmt->fetchColumn();

if (!$proprietaire_id) {
    echo json_encode(['error' => "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur."]);
    exit();
}

$paiement_id = $_GET['paiement_id'];

$stmt_info_paiement = $db->prepare("
    SELECT p.paiement_id, p.bail_id, p.statut, p.montant, p.methode, p.date, l.email, l.nom AS locataire_nom, l.prenom AS locataire_prenom, g.addresse, g.numero_garage
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
    $montant = $paiement_info['montant'];
    $addresse_garage = $paiement_info['addresse'] . ' - ' . $paiement_info['numero_garage'];

    $template_name = 'relance_paiement';
    $stmt = $db->prepare("SELECT subject, content FROM email_templates WHERE template_name = ?");
    $stmt->execute([$template_name]);
    $emailTemplate = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($emailTemplate) {
        $emailSubject = $emailTemplate['subject'] ?? 'Relance de paiement';
        $emailContent = $emailTemplate['content'] ?? '';
        $emailContent = str_replace('{locataire_nom}', $locataire_nom, $emailContent);
        $emailContent = str_replace('{addresse_garage}', $addresse_garage, $emailContent);
        $emailContent = str_replace('{montant}', $montant, $emailContent);
    } else {
        $emailSubject = 'Relance de paiement';
        $emailContent = '';
    }

    echo json_encode([
        'subject' => $emailSubject,
        'content' => $emailContent
    ]);
} else {
    echo json_encode(['error' => "Aucune information trouvée pour le paiement ID $paiement_id."]);
}
?>
