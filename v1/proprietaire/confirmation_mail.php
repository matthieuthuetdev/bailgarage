<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/db.php';

// Vérifier si le paramètre 'template_name' et 'bail_id' sont présents
$template_name = $_GET['template_name'] ?? 'bail_pdf';
$bail_id = $_GET['bail_id'] ?? null;

if (empty($bail_id)) {
    echo json_encode(['error' => "Bail ID est manquant."]);
    exit();
}

// Récupérer les informations nécessaires depuis la base de données
$stmt_bail = $db->prepare("
    SELECT b.*, g.addresse, p.email AS proprietaire_email
    FROM baux b
    JOIN garages g ON b.garage_id = g.garage_id
    JOIN proprietaires p ON g.proprietaire_id = p.proprietaire_id
    WHERE b.bail_id = ?
");
$stmt_bail->execute([$bail_id]);
$bail = $stmt_bail->fetch(PDO::FETCH_ASSOC);

if (!$bail) {
    echo json_encode(['error' => "Bail non trouvé pour l'ID spécifié."]);
    exit();
}


$stmt_locataire = $db->prepare("SELECT nom, prenom, email FROM locataires WHERE locataire_id = ?");
$stmt_locataire->execute([$bail['locataire_id']]);
$locataire = $stmt_locataire->fetch(PDO::FETCH_ASSOC);

if (!$locataire) {
    echo json_encode(['error' => "Locataire non trouvé pour ce bail."]);
    exit();
}

$stmt_garage = $db->prepare("SELECT addresse FROM garages WHERE garage_id = ?");
$stmt_garage->execute([$bail['garage_id']]);
$garage = $stmt_garage->fetch(PDO::FETCH_ASSOC);

// Récupérer le modèle de l'email
$stmt_template = $db->prepare("SELECT subject, content FROM email_templates WHERE template_name = ?");
$stmt_template->execute([$template_name]);
$emailTemplate = $stmt_template->fetch(PDO::FETCH_ASSOC);

if ($emailTemplate) {
    // Remplacement des placeholders par les données réelles
    $content = str_replace([
        '{prenom_locataire}', 
        '{nom_locataire}', 
        '{adresse_garage}', 
        '{proprietaire_mail}', 
        '{date_debut}',
        '{bail_id}'
    ], [
        $locataire['prenom'], 
        $locataire['nom'], 
        $garage['addresse'], 
        $bail['proprietaire_email'],  
        $bail['date_debut'],
        $bail_id
    ], $emailTemplate['content']);

    echo json_encode([
        'subject' => $emailTemplate['subject'] ?? '',
        'content' => $content
    ]);
} else {
    echo json_encode([
        'subject' => 'Sujet par défaut',
        'content' => 'Contenu par défaut'
    ]);
}
?>
