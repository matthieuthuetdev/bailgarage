<?php
include_once '../includes/db.php';

// Vérifier si le paramètre 'template_name' est présent
$template_name = isset($_GET['template_name']) ? $_GET['template_name'] : '';

// Récupérer le contenu de l'email de relance depuis la base de données
if (!empty($template_name)) {
    $stmt = $db->prepare("SELECT subject, content FROM email_templates WHERE template_name = ?");
    $stmt->execute([$template_name]);
    $emailTemplate = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($emailTemplate) {
        echo json_encode([
            'subject' => $emailTemplate['subject'] ?? '',
            'content' => $emailTemplate['content'] ?? ''
        ]);
    } else {
        echo json_encode([
            'subject' => 'Nouveau Locataire', // Valeur par défaut si aucune donnée trouvée
            'content' => ''
        ]);
    }
} else {
    echo json_encode([
        'subject' => 'Nouveau Locataire',
        'content' => ''
    ]);
}
?>
