<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/db.php';

if (isset($_GET['id'])) {
    $proprietaire_id = $_GET['id'];

    $stmt = $db->prepare("SELECT piece_jointe_path, piece_jointe_nom FROM proprietaires WHERE proprietaire_id = ?");
    $stmt->bindParam(1, $proprietaire_id, PDO::PARAM_INT);
    $stmt->execute();
    $proprietaire = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if ($proprietaire && $proprietaire['piece_jointe_path']) {
        $piece_jointe_path = $proprietaire['piece_jointe_path'];
        $piece_jointe_nom = $proprietaire['piece_jointe_nom'];

        if (file_exists($piece_jointe_path)) {
            // Déterminer le type MIME en fonction de l'extension du fichier
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $piece_jointe_type = finfo_file($finfo, $piece_jointe_path);
            finfo_close($finfo);

            header("Content-Type: " . $piece_jointe_type);
            header('Content-Disposition: inline; filename="' . $piece_jointe_nom . '"');

            // Lire le fichier et l'envoyer au navigateur
            readfile($piece_jointe_path);
            exit();
        } else {
            echo "Fichier non trouvé sur le serveur.";
        }
    } else {
        echo "Aucune pièce jointe trouvée pour ce propriétaire.";
    }
} else {
    echo "ID de propriétaire non fourni.";
}
?>
