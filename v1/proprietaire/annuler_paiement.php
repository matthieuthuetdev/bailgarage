<?php
session_start();
include_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    echo json_encode(['error' => 'Utilisateur non autorisé.']);
    exit();
}

$paiement_id = $_POST['paiement_id'] ?? '';

if (empty($paiement_id)) {
    echo json_encode(['error' => 'ID de paiement manquant.']);
    exit();
}

// Mettre à jour le statut du paiement à "impayé", la méthode à NULL et la date de paiement à NULL
$stmt = $db->prepare("UPDATE paiements SET statut = 'impayé', methode = NULL, date=0000-00-00 WHERE paiement_id = ?");
if ($stmt->execute([$paiement_id])) {
    echo json_encode(['success' => 'Paiement annulé avec succès.']);
} else {
    echo json_encode(['error' => 'Erreur lors de l\'annulation du paiement.']);
}
?>
