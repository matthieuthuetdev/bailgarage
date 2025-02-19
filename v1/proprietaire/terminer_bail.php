<?php
session_start();
include_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

if (isset($_POST['bail_id']) && isset($_POST['date_fin'])) {
    $bail_id = $_POST['bail_id'];
    $date_fin = $_POST['date_fin'];
    $prorata_fin = $_POST['prorata_fin'];

    // Mettre à jour le bail pour le marquer comme terminé et définir la date de fin
    $sql = "UPDATE baux SET status = 'terminated', date_fin = :date_fin, prorata_fin = :prorata_fin WHERE bail_id = :bail_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':bail_id', $bail_id);
    $stmt->bindParam(':date_fin', $date_fin);
    $stmt->bindParam(':prorata_fin', $prorata_fin);
    $stmt->execute();

    // Rediriger ou afficher un message de confirmation
    header("Location: liste_baux.php");
    exit();
}
?>

