<?php
session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $proprietaire_id = intval($_POST['proprietaire_id']);

    if ($email && $proprietaire_id) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM locataires WHERE email = ? AND proprietaire_id = ?");
        $stmt->execute([$email, $proprietaire_id]);
        $count = $stmt->fetchColumn();

        echo json_encode(['exists' => $count > 0]);
    } else {
        echo json_encode(['exists' => false]);
    }
}
?>
