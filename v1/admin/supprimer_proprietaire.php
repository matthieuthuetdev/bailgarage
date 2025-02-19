<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: connexion_admin
	.php"); // Redirige s'il n'est pas connecté en tant que propriétaire
    exit();
}

// Vérifier si l'ID du locataire est passé en paramètre
if (isset($_GET['id'])) {
    $proprietaire_id = $_GET['id'];

    // Supprimer le locataire de la base de données
    $sql = "DELETE FROM proprietaires WHERE proprietaire_id = :proprietaire_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':proprietaire_id', $proprietaire_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Proprietaire supprimé avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du proprietaire.";
    }
} else {
    $_SESSION['error_message'] = "ID de proprietaire manquant.";
}

header("Location: liste_proprietaires.php");
exit();
?>
