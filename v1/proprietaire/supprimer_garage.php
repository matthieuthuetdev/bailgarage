<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un proprietaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php"); // Redirige s'il n'est pas connecté en tant que proprio
    exit();
}

// Vérifier si l'ID du garage est passé en paramètre
if (isset($_GET['id'])) {
    $garage_id = $_GET['id'];

    // Supprimer le garage de la base de données
    $sql = "DELETE FROM garages WHERE garage_id = :garage_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':garage_id', $garage_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Garage supprimé avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du garage.";
    }
} else {
    $_SESSION['error_message'] = "ID de garage manquant.";
}

header("Location: garage.php");
exit();
?>
