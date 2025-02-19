<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php'; 

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../admin/connexion_admin.php"); // Redirige s'il n'est pas connecté en tant qu'admin
    exit();
}

if (!isset($_GET['locataire_id'])) {
    echo "Identifiant du locataire non spécifié.";
    exit();
}

$locataire_id = $_GET['locataire_id'];

// Récupérer les paiements pour le locataire spécifié avec le statut "payé"
$sql = "
    SELECT p.*, b.bail_id, l.nom AS nom_locataire, l.prenom AS prenom_locataire
    FROM paiements p
    LEFT JOIN baux b ON p.bail_id = b.bail_id
    LEFT JOIN locataires l ON b.locataire_id = l.locataire_id
    WHERE l.locataire_id = :locataire_id AND p.statut = 'payé'
    ORDER BY p.mois";

$stmt = $db->prepare($sql);
$stmt->bindParam(':locataire_id', $locataire_id, PDO::PARAM_INT);
$stmt->execute();
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($paiements)) {
    echo "Aucun paiement trouvé pour ce locataire.";
    exit();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=paiements.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Mois', 'Montant', 'Date de Paiement', 'Méthode de Paiement', 'Locataire', 'Bail ID'));

foreach ($paiements as $paiement) {
    fputcsv($output, array(
        $paiement['mois'],
        $paiement['montant'],
        $paiement['date'],
        $paiement['methode'],
        $paiement['nom_locataire'] . ' ' . $paiement['prenom_locataire'],
        $paiement['bail_id']
    ));
}

fclose($output);
exit();
?>
