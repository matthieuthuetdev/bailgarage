<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/db.php';

if (isset($_GET['id'])) {
    $garage_id = $_GET['id'];

    $stmt = $db->prepare("SELECT piece_jointe_path, piece_jointe_nom, piece_jointe_type FROM garages WHERE garage_id = ?");
    $stmt->bindParam(1, $garage_id, PDO::PARAM_INT);
    $stmt->execute();
    $garage = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if ($garage && $garage['piece_jointe_path']) {
        $piece_jointe_path = $garage['piece_jointe_path'];
        $piece_jointe_type = $garage['piece_jointe_type'];
        $piece_jointe_nom = $garage['piece_jointe_nom'];

        if (file_exists($piece_jointe_path)) {
            header("Content-Type: " . $piece_jointe_type);
            header('Content-Disposition: inline; filename="' . $piece_jointe_nom . '"');

            // Lire le fichier et l'envoyer au navigateur
            readfile($piece_jointe_path);
            exit();
        } else {
            echo "Fichier non trouvé sur le serveur.";
        }
    } else {
        echo "Aucune pièce jointe trouvée pour ce garage.";
    }
} else {
    echo "ID de garage non fourni.";
}
?>