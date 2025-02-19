<?php
session_start();
include_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: /proprietaire/connexion_proprietaire.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paiement_id = $_POST['paiement_id'];
    $method = $_POST['method'];
    $date = $_POST['date'];

    $stmt = $db->prepare("UPDATE paiements SET statut = 'payé', methode = ?, date = ? WHERE paiement_id = ?");
    $stmt->execute([$method, $date, $paiement_id]);

    header("Location: liste_paiements.php");
    exit();
} else {
    echo "Requête invalide.";
}
?>
