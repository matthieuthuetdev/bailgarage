<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et s'il a le rôle de propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: ../proprietaire/connexion_proprietaire.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer l'ID du propriétaire connecté
$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_id = $stmt->fetchColumn();

if (!$proprietaire_id) {
    echo "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
    exit();
}

// Gestion des filtres de dates
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$query = "
    SELECT p.paiement_id, p.bail_id, p.statut, p.montant, p.methode, p.date, l.nom AS locataire_nom, l.prenom AS locataire_prenom, g.addresse, g.numero_garage
    FROM paiements p
    JOIN baux b ON p.bail_id = b.bail_id
    JOIN locataires l ON b.locataire_id = l.locataire_id
    JOIN garages g ON b.garage_id = g.garage_id
    WHERE p.statut = 'payé' AND g.proprietaire_id = ?
";

$params = [$proprietaire_id];

if (!empty($start_date)) {
    $query .= " AND p.date >= ?";
    $params[] = $start_date;
}

if (!empty($end_date)) {
    $query .= " AND p.date <= ?";
    $params[] = $end_date;
}

$query .= " ORDER BY p.date DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Définir les en-têtes pour le fichier CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=paiements.csv');
$output = fopen('php://output', 'w');

// Écrire la ligne des en-têtes
fputcsv($output, ['Locataire', 'Num Garage', 'Montant', 'Date', 'Méthode']);

// Écrire les lignes de paiements
foreach ($paiements as $paiement) {
    fputcsv($output, [
        $paiement['locataire_nom'] . ' ' . $paiement['locataire_prenom'],
        $paiement['numero_garage'],
        $paiement['montant'] . ' €',
        date('d/m/Y', strtotime($paiement['date'])),
        $paiement['methode']
    ]);
}

fclose($output);
exit();
?>
