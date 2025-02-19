<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un proprietaire
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'proprietaire') {
    header("Location: /proprietaire/connexion_proprietaire.php"); // Redirige s'il n'est pas connecté en tant que proprio
    exit();
}

// Récupérer l'ID du locataire depuis l'URL
if (!isset($_GET['locataire_id'])) {
    echo "Erreur: ID du locataire non spécifié.";
    exit();
}
$locataire_id = $_GET['locataire_id'];

// Récupérer les informations du locataire depuis la base de données
$stmt = $db->prepare("SELECT nom, prenom, telephone, email, addresse, CP, ville FROM locataires WHERE locataire_id = ?");
$stmt->execute([$locataire_id]);
$locataire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$locataire) {
    echo "Erreur: Locataire non trouvé.";
    exit();
}

// Générer le contenu de la vCard
$vcard = "BEGIN:VCARD\n";
$vcard .= "VERSION:3.0\n";
$vcard .= "FN:" . htmlspecialchars($locataire['prenom']) . " " . htmlspecialchars($locataire['nom']) . "\n";
$vcard .= "N:" . htmlspecialchars($locataire['nom']) . ";" . htmlspecialchars($locataire['prenom']) . ";;;\n";
$vcard .= "TEL;TYPE=CELL:" . htmlspecialchars($locataire['telephone']) . "\n";
$vcard .= "EMAIL:" . htmlspecialchars($locataire['email']) . "\n";
$vcard .= "ADR;TYPE=HOME:;;" . htmlspecialchars($locataire['addresse']) . ";" . htmlspecialchars($locataire['ville']) . ";;" . htmlspecialchars($locataire['CP']) . ";\n";
$vcard .= "END:VCARD\n";

// Définir les en-têtes pour le téléchargement du fichier
header('Content-Type: text/vcard');
header('Content-Disposition: attachment; filename="locataire_' . htmlspecialchars($locataire['locataire_id']) . '.vcf"');
echo $vcard;
exit();
?>
