<?php
session_start();
include_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $user_id = $_SESSION['user_id'];

    // Récupération de l'ID du propriétaire
    $stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $proprietaire_id = $stmt->fetchColumn();
    $stmt->closeCursor();

    if (!$proprietaire_id) {
        echo json_encode(['exists' => false, 'error' => 'ID du propriétaire non trouvé.']);
        exit();
    }

    // Vérification de l'email
    $stmt_check_email = $db->prepare("SELECT COUNT(*) FROM locataires WHERE email = ? AND proprietaire_id = ?");
    $stmt_check_email->execute([$email, $proprietaire_id]);
    $count_email = $stmt_check_email->fetchColumn();
    $stmt_check_email->closeCursor();

    echo json_encode(['exists' => $count_email > 0]);
    exit();
}
?>
