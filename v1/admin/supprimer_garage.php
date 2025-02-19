<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php"); // Redirige s'il n'est pas connecté en tant qu'admin
    exit();
}

// Vérifier si l'ID du garage est passé en paramètre
if (isset($_GET['id'])) {
    $garage_id = intval($_GET['id']);

    // Récupérer l'ID du propriétaire avant de supprimer le garage
    $stmt = $db->prepare("SELECT proprietaire_id FROM garages WHERE garage_id = :garage_id");
    $stmt->bindParam(':garage_id', $garage_id, PDO::PARAM_INT);
    $stmt->execute();
    $proprietaire_id = $stmt->fetchColumn();

    if ($proprietaire_id) {
        // Supprimer le garage de la base de données
        $sql = "DELETE FROM garages WHERE garage_id = :garage_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':garage_id', $garage_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Garage supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression du garage.";
        }
    } else {
        $_SESSION['error_message'] = "Garage non trouvé ou propriétaire non valide.";
    }
} else {
    $_SESSION['error_message'] = "ID de garage manquant.";
}

// Redirection vers la page de gestion des garages avec l'ID du propriétaire
header("Location: garages.php?proprietaire_id=$proprietaire_id");
exit();
?>

