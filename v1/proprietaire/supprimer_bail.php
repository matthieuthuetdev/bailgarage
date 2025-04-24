<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

if (isset($_GET['delete_bail']) && isset($_GET['bail_id'])) {
    $bail_id = $_GET['bail_id'];

    // Supprimer le bail de la base de données
    $sql = "DELETE FROM baux WHERE bail_id = :bail_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':bail_id', $bail_id);
    $stmt->execute();

    // Rediriger ou afficher un message de confirmation
    header("Location: liste_baux.php");
    exit();
}
?>
