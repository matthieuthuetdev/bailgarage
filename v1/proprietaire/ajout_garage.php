<?php

ini_set('display_errors', 0); // Désactive l'affichage des erreurs
ini_set('log_errors', 1); // Active l'enregistrement des erreurs
ini_set('error_log', '../../logs/app.bailgarage.fr/error_log'); // Chemin vers le fichier de log des erreurs

include_once '../includes/db.php';
session_start();

// Redirection si l'utilisateur n'est pas connecté ou n'est pas un propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Préparer la requête pour éviter les injections SQL
$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$proprietaire_id = $stmt->fetchColumn();
$stmt->closeCursor();

if (!$proprietaire_id) {
    echo "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Utiliser filter_input pour nettoyer les données reçues
    $addresse = filter_input(INPUT_POST, 'addresse', FILTER_SANITIZE_STRING);
    $complement = filter_input(INPUT_POST, 'complement', FILTER_SANITIZE_STRING);
    $CP = filter_input(INPUT_POST, 'CP', FILTER_SANITIZE_STRING);
    $ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING);
    $pays = filter_input(INPUT_POST, 'pays', FILTER_SANITIZE_STRING);
    $numero_garage = filter_input(INPUT_POST, 'numero_garage', FILTER_SANITIZE_STRING);
    $numero_lot = filter_input(INPUT_POST, 'numero_lot', FILTER_SANITIZE_STRING);
    $loyer_hors_charge = filter_input(INPUT_POST, 'loyer_hors_charge', FILTER_SANITIZE_NUMBER_INT);
    $charge = filter_input(INPUT_POST, 'charge', FILTER_SANITIZE_NUMBER_INT);
    $caution = filter_input(INPUT_POST, 'caution', FILTER_SANITIZE_NUMBER_INT);
    $surface = filter_input(INPUT_POST, 'surface', FILTER_SANITIZE_NUMBER_INT);
    $commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
    $syndic = filter_input(INPUT_POST, 'syndic', FILTER_SANITIZE_STRING);
    $reference = filter_input(INPUT_POST, 'reference', FILTER_SANITIZE_STRING);

    $piece_jointe_path = null;
    $piece_jointe_nom = null;

    if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] == UPLOAD_ERR_OK) {
        // Gérer le téléchargement de fichiers
        $piece_jointe_tmp = $_FILES['piece_jointe']['tmp_name'];
        $piece_jointe_nom = $_FILES['piece_jointe']['name'];
        $piece_jointe_type = $_FILES['piece_jointe']['type'];
        
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $piece_jointe_path = $upload_dir . basename($piece_jointe_nom);

        if ($_FILES['piece_jointe']['size'] > 2000000) {
            $message = "Erreur: La taille du fichier dépasse la limite autorisée de 2MB.";
        }

        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($piece_jointe_type, $allowed_types)) {
            $message = "Erreur: Seuls les fichiers JPG, PNG et PDF sont autorisés.";
        }

        if (empty($message) && !move_uploaded_file($piece_jointe_tmp, $piece_jointe_path)) {
            $message = "Erreur: Impossible de déplacer le fichier téléchargé.";
        }
    }

    if (empty($message)) {
        try {
            $stmt = $db->prepare("INSERT INTO garages (proprietaire_id, addresse, complement, CP, ville, pays, numero_garage, numero_lot, loyer_hors_charge, charge, caution, surface, commentaire, syndic, reference, piece_jointe_path, piece_jointe_nom) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$proprietaire_id, $addresse, $complement, $CP, $ville, $pays, $numero_garage, $numero_lot, $loyer_hors_charge, $charge, $caution, $surface, $commentaire, $syndic, $reference, $piece_jointe_path, $piece_jointe_nom]);
            header("Location: garage.php");
            exit();
        } catch (PDOException $e) {
            $message = "Erreur PDO: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    

    <div class="container">
		<nav>
        <ul>
            <li>
                <a href="proprietaire_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a>
                <ul>
                    <li><a href="garage.php" title="Gérer vos garages"><i class="fas fa-warehouse"></i> Garages</a></li>
                    <li><a href="locataires.php" title="Gérer vos locataires"><i class="fas fa-users"></i> Locataires</a></li>
                    <li><a href="liste_baux.php" title="Gérer les baux"><i class="fas fa-file-contract"></i> Baux</a></li>
                    <li><a href="liste_paiements.php" title="Gérer les paiements"><i class="fas fa-money-check-alt"></i> Paiements</a></li>
                </ul>
            </li>
            <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
            <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </nav>
		<br>
        <h2><i class="fas fa-plus"></i> Ajouter un Garage</h2>
        <?php if (!empty($message)) : ?>
            <p class="error-message"><?= $message ?></p>
        <?php endif; ?>
        <form action="ajout_garage.php" method="post" enctype="multipart/form-data">
			
            <div class="flex-container">
                <div>
                    <label for="addresse"><i class="fas fa-map-marker-alt"></i> Adresse*:</label>
                    <input type="text" id="addresse" name="addresse" required>
                </div>
                <div>
                    <label for="complement"><i class="fas fa-building"></i> Complément:</label>
                    <input type="text" id="complement" name="complement">
                </div>
            </div>
            
            <div class="flex-container">
				
                <div>
                    <label for="CP"><i class="fas fa-envelope"></i> Code Postal*:</label>
                    <input type="text" id="CP" name="CP" required>
                </div>
                <div>
                    <label for="ville"><i class="fas fa-city"></i> Ville*:</label>
                    <input type="text" id="ville" name="ville" required>
                </div>
                <div>
                    <label for="pays"><i class="fas fa-flag"></i> Pays*:</label>
                    <input type="text" id="pays" name="pays" value="France" required>
                </div>
            </div>
            
            <div class="flex-container">
                <div>
                    <label for="numero_garage"><i class="fas fa-parking"></i> Numéro de Garage*:</label>
                    <input type="text" id="numero_garage" name="numero_garage" required>
                </div>
                <div>
                    <label for="numero_lot"><i class="fas fa-th"></i> Numéro de Lot*:</label>
                    <input type="text" id="numero_lot" name="numero_lot" required>
                </div>
				<div>
                    <label for="surface"><i class="fas fa-ruler-combined"></i> Surface*:</label>
                    <input type="number" id="surface" name="surface">
                </div>
            </div>

            <div class="flex-container">
                
                <div>
                    <label for="loyer_hors_charge"><i class="fas fa-euro-sign"></i> Loyer Hors Charge*:</label>
                    <input type="number" id="loyer_hors_charge" name="loyer_hors_charge" required>
                </div>
				<div>
                    <label for="charge"><i class="fas fa-euro-sign"></i> Charge*:</label>
                    <input type="number" id="charge" name="charge" required>
                </div>
                <div>
                    <label for="caution"><i class="fas fa-lock"></i> Caution*:</label>
                    <input type="number" id="caution" name="caution" required>
                </div>
				
            </div>
            
            <div>
                <label for="reference"><i class="fas fa-receipt"></i> Référence de virement:</label>
                <input type="text" id="reference" name="reference" >
            </div>

            

            <div>
                <label for="syndic"><i class="fas fa-info-circle"></i> Info Syndic:</label>
                <textarea id="syndic" name="syndic"></textarea>
            </div>
	<div>
                <label for="commentaire"><i class="fas fa-comments"></i> Commentaire:</label>
                <textarea id="commentaire" name="commentaire"></textarea>
            </div>
            <div>
                <label for="piece_jointe"><i class="fas fa-paperclip"></i> Pièce Jointe:</label>
                <input type="file" id="piece_jointe" name="piece_jointe">
            </div>

            <div class="flex-container">
                <button type="submit" name="submit"><i class="fas fa-plus"></i> Ajouter Garage</button>
                <button type="button" onclick="history.back()"><i class="fas fa-times"></i> Annuler</button>
            </div>
        </form>
    </div>
</body>
</html>
