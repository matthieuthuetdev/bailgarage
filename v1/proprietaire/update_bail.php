<?php
include_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

$bail_id = $_POST['bail_id'] ?? null;
$fait_le = $_POST['fait_le'] ?? null;
$fait_a = $_POST['fait_a'] ?? null;
$date_debut = $_POST['date_debut'] ?? null;
$nombre_de_clefs = $_POST['nombre_de_clefs'] ?? null;
$nombre_de_bips = $_POST['nombre_de_bips'] ?? null;
$montant_loyer = $_POST['montant_loyer'] ?? null;
$montant_charges = $_POST['montant_charges'] ?? null;
$total_mensuel = $_POST['total_mensuel'] ?? null;
$prorata = $_POST['prorata'] ?? null;
$caution = $_POST['caution'] ?? null;

$stmt = $db->prepare("UPDATE baux SET fait_le = ?, fait_a = ?, date_debut = ?, nombre_de_clefs = ?, nombre_de_bips = ?, montant_loyer = ?, montant_charges = ?, total_mensuel = ?, prorata = ?, caution = ? WHERE bail_id = ?");
$stmt->bindParam(1, $fait_le, PDO::PARAM_STR);
$stmt->bindParam(2, $fait_a, PDO::PARAM_STR);
$stmt->bindParam(3, $date_debut, PDO::PARAM_STR);
$stmt->bindParam(4, $nombre_de_clefs, PDO::PARAM_INT);
$stmt->bindParam(5, $nombre_de_bips, PDO::PARAM_INT);
$stmt->bindParam(6, $montant_loyer, PDO::PARAM_STR);
$stmt->bindParam(7, $montant_charges, PDO::PARAM_STR);
$stmt->bindParam(8, $total_mensuel, PDO::PARAM_STR);
$stmt->bindParam(9, $prorata, PDO::PARAM_STR);
$stmt->bindParam(10, $caution, PDO::PARAM_STR);
$stmt->bindParam(11, $bail_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    header("Location: confirmation.php?id=" . $bail_id);
    exit();
} else {
    echo "Erreur lors de la mise à jour: " . $stmt->errorInfo()[2];
}
