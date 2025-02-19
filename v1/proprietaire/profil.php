<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et est un propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql_user = "SELECT * FROM users WHERE user_id = :user_id";
$stmt_user = $db->prepare($sql_user);
$stmt_user->bindParam(':user_id', $user_id);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Récupérer les informations du propriétaire
$sql_proprietaire = "SELECT * FROM proprietaires WHERE user_id = :user_id";
$stmt_proprietaire = $db->prepare($sql_proprietaire);
$stmt_proprietaire->bindParam(':user_id', $user_id);
$stmt_proprietaire->execute();
$proprietaire = $stmt_proprietaire->fetch(PDO::FETCH_ASSOC);

// Mise à jour des informations de profil via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax']) && $_POST['ajax'] == 'update_profile') {
	$genre = $_POST['genre'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $adresse = $_POST['adresse'];
	$complement = $_POST['complement'];
    $cp = $_POST['CP'];
    $ville = $_POST['ville'];
    $pays = $_POST['pays'];
    $telephone = $_POST['telephone'];

    // Gérer l'upload de la pièce jointe
    $piece_jointe_path = $proprietaire['piece_jointe_path'];
    $piece_jointe_nom = $proprietaire['piece_jointe_nom'];

    if (isset($_FILES['pj']) && $_FILES['pj']['error'] == UPLOAD_ERR_OK) {
        $piece_jointe_tmp = $_FILES['pj']['tmp_name'];
        $piece_jointe_nom = basename($_FILES['pj']['name']);
        $piece_jointe_type = $_FILES['pj']['type'];

        // Définir le chemin où le fichier sera stocké
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Vérifier la taille du fichier
        if ($_FILES['pj']['size'] > 2000000) { // Limite de taille à 2MB
            echo json_encode(['success' => false, 'message' => 'Erreur: La taille du fichier dépasse la limite autorisée de 2MB.', 'type' => 'error']);
            exit();
        }

        // Vérifier le type de fichier
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($piece_jointe_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Erreur: Seuls les fichiers JPG, PNG et PDF sont autorisés.', 'type' => 'error']);
            exit();
        }

        // Si pas d'erreur, déplacer le fichier
        $piece_jointe_nom = $user_id . '_' . $piece_jointe_nom;
        $piece_jointe_path = $upload_dir . $piece_jointe_nom;

        if (!move_uploaded_file($piece_jointe_tmp, $piece_jointe_path)) {
            echo json_encode(['success' => false, 'message' => 'Erreur: Impossible de déplacer le fichier téléchargé.', 'type' => 'error']);
            exit();
        }
    }

    // Ajout de débogage pour voir les valeurs avant la mise à jour
    error_log("piece_jointe_path avant mise à jour: " . var_export($piece_jointe_path, true));
    error_log("piece_jointe_nom avant mise à jour: " . var_export($piece_jointe_nom, true));

    // Mettre à jour les informations du propriétaire
    $sql_update_proprietaire = "UPDATE proprietaires SET genre = :genre, nom = :nom, prenom = :prenom, addresse = :adresse, complement = :complement, CP = :cp, ville = :ville, pays = :pays, telephone = :telephone, piece_jointe_path = :piece_jointe_path, piece_jointe_nom = :piece_jointe_nom WHERE user_id = :user_id";
    $stmt_update_proprietaire = $db->prepare($sql_update_proprietaire);
	$stmt_update_proprietaire->bindParam(':genre', $genre);
    $stmt_update_proprietaire->bindParam(':nom', $nom);
    $stmt_update_proprietaire->bindParam(':prenom', $prenom);
    $stmt_update_proprietaire->bindParam(':adresse', $adresse);
	$stmt_update_proprietaire->bindParam(':complement', $complement);
    $stmt_update_proprietaire->bindParam(':cp', $cp);
    $stmt_update_proprietaire->bindParam(':ville', $ville);
    $stmt_update_proprietaire->bindParam(':pays', $pays);
    $stmt_update_proprietaire->bindParam(':telephone', $telephone);
    $stmt_update_proprietaire->bindValue(':piece_jointe_path', $piece_jointe_path, $piece_jointe_path === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt_update_proprietaire->bindValue(':piece_jointe_nom', $piece_jointe_nom, $piece_jointe_nom === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt_update_proprietaire->bindParam(':user_id', $user_id);

    if ($stmt_update_proprietaire->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès.', 'type' => 'success']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du profil.', 'type' => 'error']);
    }
    exit();
}

// Mise à jour des informations de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax']) && $_POST['ajax'] == 'update_user') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas.', 'type' => 'error']);
    } else {
        // Mettre à jour l'email
        $sql_update_user = "UPDATE users SET email = :email WHERE user_id = :user_id";
        $stmt_update_user = $db->prepare($sql_update_user);
        $stmt_update_user->bindParam(':email', $email);
        $stmt_update_user->bindParam(':user_id', $user_id);

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_update_password = "UPDATE users SET password = :password WHERE user_id = :user_id";
            $stmt_update_password = $db->prepare($sql_update_password);
            $stmt_update_password->bindParam(':password', $hashed_password);
            $stmt_update_password->bindParam(':user_id', $user_id);
            $stmt_update_password->execute();
        }

        if ($stmt_update_user->execute()) {
            echo json_encode(['success' => true, 'message' => 'Informations de connexion mises à jour avec succès.', 'type' => 'success']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour des informations de connexion.', 'type' => 'error']);
        }
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<style>
        
    </style>
</head>
<body>
    

    <div id="message" class=""></div>

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
    </nav><br>
		
        <h2>Informations Propriétaire</h2>
        <form id="updateForm" action="profil.php" method="POST" enctype="multipart/form-data">
			
			<div>
    <label for="genre"><i class="fas fa-venus-mars"></i> Genre*:</label>
    <div class="radio-container">
        <input type="radio" id="genre_m" name="genre" value="M" <?php echo ($proprietaire['genre'] == 'M') ? 'checked' : ''; ?> required>
        <label for="genre_m"><i class="fas fa-male"></i> M</label>
        <input type="radio" id="genre_mme" name="genre" value="MME" <?php echo ($proprietaire['genre'] == 'MME') ? 'checked' : ''; ?> required>
        <label for="genre_mme"><i class="fas fa-female"></i> MME</label>
    </div>
</div>


				
			<div class="flex-container">
			<div>
            <label for="nom"><i class="fas fa-user"></i> Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($proprietaire['nom']); ?>">
				</div>
			<div>
            <label for="prenom"><i class="fas fa-user"></i> Prénom:</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($proprietaire['prenom']); ?>">
				</div>
			</div>
			
			<div class="flex-container">
			<div>
            <label for="adresse"><i class="fas fa-map-marker-alt"></i> Adresse:</label>
            <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($proprietaire['addresse']); ?>">
				</div>
				<div>
                    <label for="complement"><i class="fas fa-building"></i> Complément:</label>
                    <input type="text" id="complement" name="complement" value="<?php echo htmlspecialchars($proprietaire['complement']); ?>">
                </div>
			</div>

			<div class="flex-container">
			<div>
            <label for="CP"><i class="fas fa-envelope"></i> Code Postal:</label>
            <input type="text" id="CP" name="CP" value="<?php echo htmlspecialchars($proprietaire['CP']); ?>">
			</div>
			
			<div>
            <label for="ville"><i class="fas fa-city"></i> Ville:</label>
            <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($proprietaire['ville']); ?>">
				</div>
			
			<div>
            <label for="pays"><i class="fas fa-flag"></i> Pays:</label>
            <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($proprietaire['pays']); ?>">
				</div>
			</div>
			
			
			<div>
            <label for="telephone"><i class="fas fa-phone"></i> Téléphone:</label>
            <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($proprietaire['telephone']); ?>">
			</div>
			
            <div>
            <label for="pj"><i class="fas fa-paperclip"></i> Ajouter/Modifier la Signature:</label>
            <input type="file" id="pj" name="pj" accept=".jpg,.jpeg,.png,.pdf">
			</div>
            
            <?php if (!empty($proprietaire['piece_jointe_nom'])): ?>
    <label>Pièce jointe actuelle: 
        <a href="ouvrir_piece_jointe.php?id=<?php echo $proprietaire['proprietaire_id']; ?>" target="_blank">
            <?php echo htmlspecialchars($proprietaire['piece_jointe_nom']); ?>
        </a>
    </label>
<?php endif; ?>


            
            <input type="hidden" name="ajax" value="update_profile">
            <div class="flex-container">
                <button type="submit"><i class="fas fa-save"></i> Modifier</button>
                <button type="button" onclick="history.back()"><i class="fas fa-times"></i> Retour</button>
            </div>
        </form>
    </div>

    <div class="container">
        <h2>Informations de connexion</h2>
        <form id="updateCon" action="profil.php" method="POST">
			
			<div>
            <label for="email"><i class="fas fa-envelope"></i> Adresse électronique:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
			</div>

			<div class="flex-container">
			<div>
            <label for="password"><i class="fas fa-lock"></i> Mot de passe:</label>
            <input type="password" id="password" name="password">
				</div>
			<div>

            <label for="confirm_password"><i class="fas fa-lock"></i> Confirmer le mot de passe:</label>
            <input type="password" id="confirm_password" name="confirm_password">
				</div>
			</div>

            <input type="hidden" name="ajax" value="update_user">
            <div class="flex-container">
                <button type="submit"><i class="fas fa-save"></i> Modifier</button>
                <button type="button" onclick="history.back()"><i class="fas fa-times"></i> Retour</button>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('updateForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche le formulaire de soumettre de manière classique

        const formData = new FormData(this); // Récupère les données du formulaire

        fetch('profil.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // On attend une réponse JSON
        .then(data => {
            const messageDiv = document.getElementById('message');
            if (data.success) {
                messageDiv.innerText = data.message;
                messageDiv.className = 'success-message';
                // Actualise les champs du formulaire avec les nouvelles valeurs
                document.getElementById('nom').value = formData.get('nom');
                document.getElementById('prenom').value = formData.get('prenom');
                document.getElementById('adresse').value = formData.get('adresse');
                document.getElementById('CP').value = formData.get('CP');
                document.getElementById('ville').value = formData.get('ville');
                document.getElementById('pays').value = formData.get('pays');
                document.getElementById('telephone').value = formData.get('telephone');
            } else {
                messageDiv.innerText = data.message;
                messageDiv.className = 'error-message';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            const messageDiv = document.getElementById('message');
            messageDiv.innerText = "Erreur lors de la mise à jour du profil.";
            messageDiv.className = 'error-message';
        });
    });
    
    document.getElementById('updateCon').addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche la soumission normale du formulaire
    
        const formData = new FormData(this); // Récupère les données du formulaire
    
        fetch('profil.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('message');
            if (data.success) {
                messageDiv.innerText = data.message;
                messageDiv.className = 'success-message';
                // Mettre à jour l'email affiché dans le champ
                document.getElementById('email').value = formData.get('email');
            } else {
                messageDiv.innerText = data.message;
                messageDiv.className = 'error-message';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            const messageDiv = document.getElementById('message');
            messageDiv.innerText = "Erreur lors de la mise à jour des informations de connexion.";
            messageDiv.className = 'error-message';
        });
    });
    </script>
</body>
</html>

