<?php
ini_set('display_errors', 0); // Désactive l'affichage des erreurs
ini_set('log_errors', 1); // Active l'enregistrement des erreurs
ini_set('error_log', '../../logs/app.bailgarage.fr/error_log'); // Chemin vers le fichier de log des erreurs

include_once '../includes/db.php';
session_start();

// Vérification que l'utilisateur est connecté et a le rôle approprié
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php");
    exit();
}

// Récupérer l'ID du propriétaire et du garage depuis l'URL
$proprietaire_id = filter_input(INPUT_GET, 'proprietaire_id', FILTER_VALIDATE_INT);
$garage_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $db->prepare("SELECT *, IFNULL(LENGTH(piece_jointe_path), 0) AS piece_jointe_length FROM garages WHERE garage_id = ?");
$stmt->bindParam(1, $garage_id, PDO::PARAM_INT);
$stmt->execute();
$garage = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$garage) {
    echo "Garage non trouvé.";
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nettoyage et validation des données entrantes
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

    $piece_jointe_path = $garage['piece_jointe_path'];
    $piece_jointe_nom = $garage['piece_jointe_nom'];
    $supprimer_piece_jointe = isset($_POST['supprimer_piece_jointe']) && $_POST['supprimer_piece_jointe'] == '1';

    if ($supprimer_piece_jointe) {
        if (file_exists($piece_jointe_path)) {
            unlink($piece_jointe_path);
        }
        $piece_jointe_path = null;
        $piece_jointe_nom = null;
    }

    if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] == UPLOAD_ERR_OK) {
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
        $stmt = $db->prepare("UPDATE garages SET addresse=?, complement=?, CP=?, ville=?, pays=?, numero_garage=?, numero_lot=?, loyer_hors_charge=?, charge=?, caution=?, surface=?, commentaire=?, syndic=?, reference=?, piece_jointe_path=?, piece_jointe_nom=? WHERE garage_id=?");
        $params = [$addresse, $complement, $CP, $ville, $pays, $numero_garage, $numero_lot, $loyer_hors_charge, $charge, $caution, $surface, $commentaire, $syndic, $reference, $piece_jointe_path, $piece_jointe_nom, $garage_id];
        if ($stmt->execute($params)) {
            header("Location: garages.php?proprietaire_id=$proprietaire_id&success=1");
            exit();
        } else {
            $message = "Erreur: " . $stmt->errorInfo()[2];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
	<link rel="stylesheet" href="../css/form.css">
</head>
<body>
    

    <div class="container">
		 <nav>
            <ul> 
                <li>
                    <a href="admin_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a>
                </li>
                <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>  
            </ul>
        </nav>
		<br>
        <h2><i class="fas fa-pencil-alt"></i> Modifier le Garage</h2>
        <?php if (!empty($message)) : ?>
            <p class="success-message" style="color: green;"><?= $message ?></p>
        <?php endif; ?>
            <form action="modifier_garage.php?id=<?php echo $garage_id; ?>&proprietaire_id=<?php echo $proprietaire_id; ?>" method="post" enctype="multipart/form-data">
            <div class="flex-container">
                <div>
                    <label for="addresse"><i class="fas fa-map-marker-alt"></i> Adresse*:</label>
                    <input type="text" id="addresse" name="addresse" value="<?php echo htmlspecialchars($garage['addresse']); ?>" required>
                </div>
                <div>
                    <label for="complement"><i class="fas fa-building"></i> Complément:</label>
                    <input type="text" id="complement" name="complement" value="<?php echo htmlspecialchars($garage['complement']); ?>">
                </div>
            </div>
            
            <div class="flex-container">
                <div>
                    <label for="CP"><i class="fas fa-envelope"></i> Code Postal*:</label>
                    <input type="text" id="CP" name="CP" value="<?php echo htmlspecialchars($garage['CP']); ?>" required>
                </div>
                <div>
                    <label for="ville"><i class="fas fa-city"></i> Ville*:</label>
                    <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($garage['ville']); ?>" required>
                </div>
                <div>
                    <label for="pays"><i class="fas fa-flag"></i> Pays*:</label>
                    <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($garage['pays']); ?>" required>
                </div>
            </div>
            
            <div class="flex-container">
                <div>
                    <label for="numero_garage"><i class="fas fa-parking"></i> Numéro de Garage*:</label>
                    <input type="text" id="numero_garage" name="numero_garage" value="<?php echo htmlspecialchars($garage['numero_garage']); ?>" required>
                </div>
                <div>
                    <label for="numero_lot"><i class="fas fa-th"></i> Numéro de Lot*:</label>
                    <input type="text" id="numero_lot" name="numero_lot" value="<?php echo htmlspecialchars($garage['numero_lot']); ?>" required>
                </div>
            </div>

            <div class="flex-container">
                <div>
                    <label for="surface"><i class="fas fa-ruler-combined"></i> Surface*:</label>
                    <input type="text" id="surface" name="surface" value="<?php echo htmlspecialchars($garage['surface']); ?>">
                </div>
                <div>
                    <label for="loyer_hors_charge"><i class="fas fa-euro-sign"></i> Loyer Hors Charge*:</label>
                    <input type="text" id="loyer_hors_charge" name="loyer_hors_charge" value="<?php echo htmlspecialchars($garage['loyer_hors_charge']); ?>" required>
                </div>
            </div>
            
            <div class="flex-container">
                <div>
                    <label for="charge"><i class="fas fa-euro-sign"></i> Charge*:</label>
                    <input type="text" id="charge" name="charge" value="<?php echo htmlspecialchars($garage['charge']); ?>">
                </div>
                <div>
                    <label for="caution"><i class="fas fa-lock"></i> Caution*:</label>
                    <input type="text" id="caution" name="caution" value="<?php echo htmlspecialchars($garage['caution']); ?>">
                </div>
            </div>
			
			<div>
                <label for="reference"><i class="fas fa-receipt"></i> Référence de virement:</label>
                <input type="text" id="reference" name="reference" value="<?php echo htmlspecialchars($garage['reference']); ?>">
            </div>

            <div>
                <label for="syndic"><i class="fas fa-info-circle"></i>Info Syndic:</label>
				<textarea id="commentaire" name="commentaire"><?php echo htmlspecialchars($garage['syndic']); ?></textarea>
            </div>
            

            <div>
                <label for="commentaire"><i class="fas fa-comments"></i> Commentaire:</label>
                <textarea id="commentaire" name="commentaire"><?php echo htmlspecialchars($garage['commentaire']); ?></textarea>
            </div>

            <div>
                <label for="piece_jointe"><i class="fas fa-paperclip"></i> Changer la Pièce Jointe:</label>
                <input type="file" id="piece_jointe" name="piece_jointe">
            </div>

            <?php if (!empty($garage['piece_jointe_path']) && $garage['piece_jointe_length'] > 0): ?>
                <div id="attachment-section">
                    <p>Pièce jointe actuelle: <a href="<?php echo htmlspecialchars($garage['piece_jointe_path']); ?>" target="_blank" id="current-attachment"><?php echo htmlspecialchars($garage['piece_jointe_nom']); ?></a></p>
                    <button type="button" id="delete-attachment-btn"><i class="fas fa-trash-alt"></i> Supprimer la pièce jointe actuelle</button><br>
                    <input type="hidden" id="supprimer_piece_jointe" name="supprimer_piece_jointe" value="0">
                </div>
            <?php endif; ?>

            <div class="flex-container">
                <button type="submit"><i class="fas fa-save"></i> Enregistrer</button>
                <button type="button" onclick="history.back()"><i class="fas fa-times"></i> Retour</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('delete-attachment-btn').addEventListener('click', function() {
            // Cacher le lien de la pièce jointe
            document.getElementById('current-attachment').classList.add('hidden');
            // Mettre à jour le champ caché pour indiquer la suppression
            document.getElementById('supprimer_piece_jointe').value = '1';
            // Cacher le bouton supprimer
            document.getElementById('delete-attachment-btn').classList.add('hidden');
        });
    </script>
</body>
</html>
