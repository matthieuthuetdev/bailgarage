<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php"); // Redirige s'il n'est pas connecté en tant que propriétaire
    exit();
}

// Vérifier si l'ID du locataire est passé en paramètre
if (isset($_GET['id'])) {
    $locataire_id = $_GET['id'];

    // Supprimer le locataire de la base de données
    $sql = "DELETE FROM locataires WHERE locataire_id = :locataire_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':locataire_id', $locataire_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Locataire supprimé avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du locataire.";
    }
} else {
    $_SESSION['error_message'] = "ID de locataire manquant.";
}

header("Location: locataires.php");
exit();
?>
