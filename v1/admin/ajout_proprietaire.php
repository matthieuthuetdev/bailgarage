<?php
session_start();
include_once '../includes/db.php';

// Vérifie que l'utilisateur est connecté et a le rôle d'administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: connexion_admin.php");
    exit();
}

// Génération du mot de passe aléatoire
$mot_de_passe = bin2hex(random_bytes(4)); // Génère une chaîne hexadécimale de 8 caractères

$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add-proprietaire'])) {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $password = password_hash($mot_de_passe, PASSWORD_DEFAULT); // Hash du mot de passe temporaire
    $role = 'proprietaire';

    try {
        $stmt_check_email = $db->prepare("SELECT COUNT(*) FROM proprietaires WHERE email = :email");
        $stmt_check_email->execute(['email' => $email]);
        $count = $stmt_check_email->fetchColumn();

        if ($count > 0) {
            $error_msg = "L'adresse email '$email' est déjà utilisée.";
        } else {
            $sql = "INSERT INTO users (nom, prenom, email, password, role) VALUES (:nom, :prenom, :email, :password, :role)";
            $stmt = $db->prepare($sql);
            $stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'password' => $password, 'role' => $role]);
            $user_id = $db->lastInsertId();

            // Autres données
            $societe = filter_input(INPUT_POST, 'societe', FILTER_SANITIZE_STRING);
            $addresse = filter_input(INPUT_POST, 'addresse', FILTER_SANITIZE_STRING);
            $complement = filter_input(INPUT_POST, 'complement', FILTER_SANITIZE_STRING);
            $genre = filter_var(trim($_POST['genre']), FILTER_SANITIZE_STRING);
            $CP = filter_input(INPUT_POST, 'CP', FILTER_SANITIZE_STRING);
            $ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING);
            $pays = filter_input(INPUT_POST, 'pays', FILTER_SANITIZE_STRING);
            $iban = filter_input(INPUT_POST, 'iban', FILTER_SANITIZE_STRING);
            $bic = filter_input(INPUT_POST, 'bic', FILTER_SANITIZE_STRING);

            $piece_jointe_path = null;
            $piece_jointe_nom = null;

            // Vérifier si un fichier a été téléchargé
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
                    $error_msg = "Erreur: La taille du fichier dépasse la limite autorisée de 2MB.";
                }

                // Vérifier le type de fichier
                $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!in_array($piece_jointe_type, $allowed_types)) {
                    $error_msg = "Erreur: Seuls les fichiers JPG, PNG et PDF sont autorisés.";
                }

                // Si pas d'erreur, déplacer le fichier
                if (empty($error_msg)) {
                    $piece_jointe_nom = $user_id . '_' . $piece_jointe_nom;
                    $piece_jointe_path = $upload_dir . $piece_jointe_nom;

                    if (!move_uploaded_file($piece_jointe_tmp, $piece_jointe_path)) {
                        $error_msg = "Erreur: Impossible de déplacer le fichier téléchargé.";
                    }
                }
            }

            if (empty($error_msg)) {
                $sql = "INSERT INTO proprietaires (user_id, nom, prenom, societe, addresse, complement, CP, ville, pays, email, telephone, iban, bic, genre, piece_jointe_path, piece_jointe_nom) VALUES (:user_id, :nom, :prenom, :societe, :addresse, :complement, :CP, :ville, :pays, :email, :telephone, :iban, :bic, :genre, :piece_jointe_path, :piece_jointe_nom)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'user_id' => $user_id, 'nom' => $nom, 'prenom' => $prenom, 'societe' => $societe,
                    'addresse' => $addresse, 'complement' => $complement, 'CP' => $CP, 'ville' => $ville, 'pays' => $pays,
                    'email' => $email, 'telephone' => $telephone, 'iban' => $iban, 'bic' => $bic, 'genre' => $genre,
                    'piece_jointe_path' => $piece_jointe_path, 'piece_jointe_nom' => $piece_jointe_nom
                ]);
					
                // Récupérer le proprietaire_id
                $stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $proprietaire_id = $stmt->fetchColumn();
				
                // Stocker les informations du propriétaire dans la session
                $_SESSION['proprietaire'] = [
                    'id' => $proprietaire_id,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'societe' => $societe,
                    'addresse' => $addresse,
                    'complement' => $complement,
                    'CP' => $CP,
                    'ville' => $ville,
                    'pays' => $pays,
                    'iban' => $iban,
                    'bic' => $bic,
                    'mot_de_passe' => $mot_de_passe
                ];

                header("Location: synthese_proprietaire.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $error_msg = "Erreur lors de l'insertion dans la base de données: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Propriétaire</title>
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function ajouterIbanBic() {
            const container = document.getElementById('iban-bic-container');
            const div = document.createElement('div');
            div.className = 'flex-container';
            div.innerHTML = `
                <div>
                    <label for="iban_supplementaire[]"><i class="fas fa-university"></i> IBAN Supplémentaire :</label>
                    <input type="text" name="iban_supplementaire[]" placeholder="IBAN Supplémentaire">
                </div>
                <div>
                    <label for="bic_supplementaire[]"><i class="fas fa-key"></i> BIC Supplémentaire :</label>
                    <input type="text" name="bic_supplementaire[]" placeholder="BIC Supplémentaire">
                </div>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
	<div class="container">
    <nav>
        <ul> 
            <li><a href="admin_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
            <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </nav>

    <h2><i class="fas fa-plus"></i> Ajouter un Propriétaire</h2>

    <!-- Affichage du message d'erreur -->
    <?php if (!empty($error_msg)): ?>
        <div class="error-message"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="ajout_proprietaire.php" method="POST" enctype="multipart/form-data">
        
        
            <div>
                <label for="genre"><i class="fas fa-venus-mars"></i> Genre*:</label>
                <div class="radio-container">
                    <input type="radio" id="genre_m" name="genre" value="M" required>
                    <label for="genre_m" style="font-weight: normal; display: inline;"><i class="fas fa-male"></i>M</label>
                    <input type="radio" id="genre_mme" name="genre" value="MME" required>
                    <label for="genre_mme" style="font-weight: normal; display: inline;"><i class="fas fa-female"></i>MME</label>
                </div>
            </div>
        
        <div class="flex-container">
            <div>
                <label for="nom"><i class="fas fa-user"></i> Nom*:</label>
                <input type="text" id="nom" name="nom" placeholder="Nom" required>
            </div>
            <div>
                <label for="prenom"><i class="fas fa-user"></i> Prénom* :</label>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
            </div>
        </div>

        <div class="flex-container">
            <div>
                <label for="email"><i class="fas fa-envelope"></i> Email*:</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div>
                <label for="telephone"><i class="fas fa-phone"></i> Téléphone :</label>
                <input type="text" id="telephone" name="telephone" placeholder="Téléphone" required>
            </div>
        </div>

        <div class="flex-container">
            <div>
                <label for="societe"><i class="fas fa-building"></i> Société :</label>
                <input type="text" id="societe" name="societe" placeholder="Société">
            </div>
            <div>
                <label for="addresse"><i class="fas fa-map-marker-alt"></i> Adresse* :</label>
                <input type="text" id="addresse" name="addresse" placeholder="Adresse" required>
            </div>
        </div>
        
        <div class="flex-container">
            <div>
                <label for="complement"><i class="fas fa-building"></i> Complément :</label>
                <input type="text" id="complement" name="complement" placeholder="Complément">
            </div>
            <div>
                <label for="CP"><i class="fas fa-envelope"></i> Code Postal* :</label>
                <input type="text" id="CP" name="CP" placeholder="Code Postal" required>
            </div>
            <div>
                <label for="ville"><i class="fas fa-city"></i> Ville* :</label>
                <input type="text" id="ville" name="ville" placeholder="Ville" required>
            </div>
            <div>
                <label for="pays"><i class="fas fa-flag"></i> Pays* :</label>
                <input type="text" id="pays" name="pays" placeholder="Pays">
            </div>
        </div>

        <div class="flex-container">
            <div>
                <label for="iban"><i class="fas fa-university"></i> IBAN* :</label>
                <input type="text" id="iban" name="iban" placeholder="IBAN" required>
            </div>
            <div>
                <label for="bic"><i class="fas fa-key"></i> BIC* :</label>
                <input type="text" id="bic" name="bic" placeholder="BIC" required>
            </div>
        </div>

        <div id="iban-bic-container"></div>
        <button type="button" class="ajout-iban-bic" onclick="ajouterIbanBic()"><i class="fas fa-plus"></i> Ajouter un IBAN/BIC Supplémentaire</button><br><br>

        <div class="flex-container">
            <div>
                <label for="pj"><i class="fas fa-file-signature"></i> Signature:</label>
                <input type="file" id="pj" name="pj" accept=".jpg,.jpeg,.png,.pdf">
            </div>
        </div>
        
        <div class="flex-container">
            <button type="submit" name="add-proprietaire"><i class="fas fa-plus"></i> Ajouter</button>
            <button type="button" onclick="history.back()"><i class="fas fa-times"></i> Annuler</button>
        </div>
    </form>
	</div>
</body>
</html>
