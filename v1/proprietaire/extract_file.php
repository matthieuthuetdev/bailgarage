<?php
include_once '../includes/db.php';

if (isset($_GET['id'])) {
    $garage_id = $_GET['id'];

    $stmt = $db->prepare("SELECT piece_jointe, piece_jointe_type, piece_jointe_nom FROM garages WHERE garage_id = ?");
    $stmt->bindParam(1, $garage_id, PDO::PARAM_INT);
    $stmt->execute();
    $garage = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if ($garage && $garage['piece_jointe']) {
        $file_path = '../extracted_files/' . $garage['piece_jointe_nom'];

        // Créer le répertoire si nécessaire
        if (!file_exists('../extracted_files')) {
            mkdir('../extracted_files', 0777, true);
        }

        // Sauvegarde du fichier
        file_put_contents($file_path, $garage['piece_jointe']);

        echo "Fichier extrait et sauvegardé à l'emplacement : " . $file_path;
    } else {
        echo "Aucune pièce jointe trouvée pour ce garage.";
    }
} else {
    echo "ID de garage non fourni.";
}
?>

