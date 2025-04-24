<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../includes/tcpdf/tcpdf.php');
include_once '../includes/db.php';
session_start();

// Vérifier que l'utilisateur connecté est un propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: ../connexion_proprietaire.php");
    exit();
}

// ID du paiement
$paiement_id = $_GET['paiement_id'] ?? null;
if (!$paiement_id) {
    echo "Paiement ID manquant.";
    exit();
}

// Récupérer les informations du paiement
$stmt = $db->prepare("SELECT * FROM paiements WHERE paiement_id = ?");
$stmt->bindParam(1, $paiement_id, PDO::PARAM_INT);
$stmt->execute();
$paiement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paiement) {
    echo "Paiement non trouvé.";
    exit();
}

// Récupérer les informations du bail
$stmt_bail = $db->prepare("SELECT * FROM baux WHERE bail_id = ?");
$stmt_bail->bindParam(1, $paiement['bail_id'], PDO::PARAM_INT);
$stmt_bail->execute();
$bail = $stmt_bail->fetch(PDO::FETCH_ASSOC);

if (!$bail) {
    echo "Bail non trouvé.";
    exit();
}

// Récupérer les informations du locataire
$stmt_locataire = $db->prepare("SELECT * FROM locataires WHERE locataire_id = ?");
$stmt_locataire->bindParam(1, $bail['locataire_id'], PDO::PARAM_INT);
$stmt_locataire->execute();
$locataire = $stmt_locataire->fetch(PDO::FETCH_ASSOC);

if (!$locataire) {
    echo "Locataire non trouvé.";
    exit();
}

// Récupérer les informations du garage
$stmt_garage = $db->prepare("SELECT * FROM garages WHERE garage_id = ?");
$stmt_garage->bindParam(1, $bail['garage_id'], PDO::PARAM_INT);
$stmt_garage->execute();
$garage = $stmt_garage->fetch(PDO::FETCH_ASSOC);

if (!$garage) {
    echo "Garage non trouvé.";
    exit();
}

// Récupérer les informations du propriétaire
$stmt_proprietaire = $db->prepare("SELECT * FROM proprietaires WHERE proprietaire_id = ?");
$stmt_proprietaire->bindParam(1, $garage['proprietaire_id'], PDO::PARAM_INT);
$stmt_proprietaire->execute();
$proprietaire = $stmt_proprietaire->fetch(PDO::FETCH_ASSOC);

if (!$proprietaire) {
    echo "Propriétaire non trouvé.";
    exit();
}

// Vérifier et préparer les dates
$date_debut = !empty($bail['date_debut']) ? date('d/m/Y', strtotime($bail['date_debut'])) : 'Date non disponible';
$date_fin = !empty($bail['date_fin']) ? date('d/m/Y', strtotime($bail['date_fin'])) : 'Date non disponible';
$date_paiement = !empty($paiement['date']) ? date('d/m/Y', strtotime($paiement['date'])) : 'Date non disponible';
$date_actuelle = date('d/m/Y');

// Créer le PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', true);
$pdf->setTitle('Quittance de loyer');
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Remplir le contenu du PDF avec les informations récupérées
$html = '
<h1>Quittance de loyer</h1>
<p>' . date('F Y') . '</p>

<p><strong>Adresse de la location :</strong><br>' . htmlspecialchars($garage['addresse']) . ' - Garage ' . htmlspecialchars($garage['numero_garage']) . ' - ' . htmlspecialchars($garage['CP']) . ' ' . htmlspecialchars($garage['ville']) . '</p>

<p>Je soussigné(e) ' . htmlspecialchars($proprietaire['nom']) . ' ' . htmlspecialchars($proprietaire['prenom']) . ', propriétaire du logement désigné ci-dessus, déclare avoir reçu de M./Mme ' . htmlspecialchars($locataire['nom']) . ' ' . htmlspecialchars($locataire['prenom']) . ' la somme de ' . htmlspecialchars($paiement['montant']) . ' euros, au titre du paiement du loyer et des charges pour la période de location du ' . $date_debut . ' au ' . $date_fin . ' et lui en donne quittance, sous réserve de tous mes droits.</p>

<p><strong>Détail du règlement :</strong></p>
<p>Loyer : ' . htmlspecialchars($bail['montant_loyer']) . ' euros<br>
Charges : ' . htmlspecialchars($bail['montant_charges']) . ' euros<br>
Total : ' . htmlspecialchars($paiement['montant']) . ' euros</p>

<p>Date du paiement : ' . $date_paiement . '</p>

<p>Fait le ' . $date_actuelle . '</p>

<p>' . htmlspecialchars($proprietaire['nom']) . ' ' . htmlspecialchars($proprietaire['prenom']) . '</p>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('bail_confirmation.pdf', 'I');

?>
