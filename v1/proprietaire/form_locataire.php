<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

$success_message = "";
$error_message = "";

// Vérification de la connexion en tant que propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

// Regénérer l'identifiant de session après l'authentification
session_regenerate_id(true);

// Récupérer l'ID de l'utilisateur depuis la session
$user_id = $_SESSION['user_id'];

// Récupérer le proprietaire_id correspondant à l'utilisateur connecté
$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_id = $stmt->fetchColumn();
$stmt->closeCursor();

// Vérification que le proprietaire_id a été trouvé
if (!$proprietaire_id) {
    $error_message = "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Filtrage et validation des entrées
    $nom = filter_var(trim($_POST['nom']), FILTER_SANITIZE_SPECIAL_CHARS);
    $prenom = filter_var(trim($_POST['prenom']), FILTER_SANITIZE_SPECIAL_CHARS);
    $addresse = filter_var(trim($_POST['addresse']), FILTER_SANITIZE_SPECIAL_CHARS);
    $complement = filter_var(trim($_POST['complement']), FILTER_SANITIZE_SPECIAL_CHARS);
    $cp = filter_var(trim($_POST['cp']), FILTER_VALIDATE_INT);
    $ville = filter_var(trim($_POST['ville']), FILTER_SANITIZE_SPECIAL_CHARS);
    $telephone = filter_var(trim($_POST['telephone']), FILTER_SANITIZE_SPECIAL_CHARS);
    $fixe = filter_var(trim($_POST['fixe']), FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $rgpd = isset($_POST['rgpd']) ? 1 : 0;
    $quittance = isset($_POST['quittance']) ? 1 : 0;
    $genre = filter_var(trim($_POST['genre']), FILTER_SANITIZE_SPECIAL_CHARS);

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
            $error_message = "Erreur: La taille du fichier dépasse la limite autorisée de 2MB.";
        }

        // Vérifier le type de fichier
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($piece_jointe_type, $allowed_types)) {
            $error_message = "Erreur: Seuls les fichiers JPG, PNG et PDF sont autorisés.";
        }
    }

    if (empty($error_message)) {
        try {
            // Début de la transaction
            $db->beginTransaction();

            // Vérifier si l'email existe déjà pour ce propriétaire
            $stmt_check = $db->prepare("SELECT COUNT(*) FROM locataires WHERE email = ? AND proprietaire_id = ?");
            $stmt_check->execute([$email, $proprietaire_id]);
            $count = $stmt_check->fetchColumn();

            if ($count > 0) {
                $error_message = "L'adresse email existe déjà pour ce propriétaire.";
            } else {
                // Insertion des données du locataire sans la pièce jointe pour obtenir l'ID du locataire
                $stmt = $db->prepare("INSERT INTO locataires (proprietaire_id, nom, prenom, addresse, complement, CP, ville, telephone, fixe, email, rgpd, quittance, genre) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$proprietaire_id, $nom, $prenom, $addresse, $complement, $cp, $ville, $telephone, $fixe, $email, $rgpd, $quittance, $genre]);

                if ($stmt->rowCount() > 0) {
                    // Récupérer l'ID du locataire nouvellement inséré
                    $locataire_id = $db->lastInsertId();

                    // Ajouter l'ID du locataire au nom du fichier pour garantir l'unicité
                    if ($piece_jointe_nom) {
                        $piece_jointe_nom = $locataire_id . '_' . $piece_jointe_nom;
                        $piece_jointe_path = $upload_dir . $piece_jointe_nom;

                        // Déplacer le fichier téléchargé vers le répertoire souhaité
                        if (!move_uploaded_file($piece_jointe_tmp, $piece_jointe_path)) {
                            $error_message = "Erreur: Impossible de déplacer le fichier téléchargé.";
                        } else {
                            // Mettre à jour le chemin et le nom de la pièce jointe pour le locataire
                            $stmt_update = $db->prepare("UPDATE locataires SET piece_jointe_path = ?, piece_jointe_nom = ? WHERE locataire_id = ?");
                            $stmt_update->execute([$piece_jointe_path, $piece_jointe_nom, $locataire_id]);
                        }
                    }

                    $success_message = "Le formulaire a été soumis avec succès.";
                    $db->commit(); // Valider la transaction
                } else {
                    $error_message = "Erreur: " . $stmt->errorInfo()[2];
                }
            }
        } catch (PDOException $e) {
            $db->rollBack(); // Annuler la transaction en cas d'erreur
            $error_message = "Erreur PDO: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire du locataire</title>
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
    </nav><br>
        <h2><i class="fas fa-user-plus"></i> Ajouter un Locataire</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form action="form_locataire.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="genre"><i class="fas fa-venus-mars"></i> Genre*:</label>
                <div class="radio-container">
                    <input type="radio" id="genre_m" name="genre" value="M" required>
                    <label for="genre_m"><i class="fas fa-male"></i> M</label>
                    <input type="radio" id="genre_mme" name="genre" value="MME" required>
                    <label for="genre_mme"><i class="fas fa-female"></i> MME</label>
                </div>
            </div>

            <div class="flex-container">
                <div>
                    <label for="nom"><i class="fas fa-user"></i> Nom*:</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div>
                    <label for="prenom"><i class="fas fa-user"></i> Prénom*:</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
            </div>

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
                    <label for="cp"><i class="fas fa-envelope"></i> Code Postal*:</label>
                    <input type="text" id="cp" name="cp" required>
                </div>
                <div>
                    <label for="ville"><i class="fas fa-city"></i> Ville*:</label>
                    <input type="text" id="ville" name="ville" required>
                </div>
            </div>

            <div class="flex-container">
                <div>
                    <label for="telephone"><i class="fas fa-mobile-alt"></i> Téléphone mobile*:</label>
                    <input type="tel" id="telephone" name="telephone" required>
                </div>
                <div>
                    <label for="fixe"><i class="fas fa-phone"></i> Téléphone fixe:</label>
                    <input type="tel" id="fixe" name="fixe">
                </div>
                <div>
                    <label for="email"><i class="fas fa-envelope"></i> Email*:</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>

            <div class="flex-container">
                <div>
                    <label for="pj"><i class="fas fa-id-card"></i> Pièce d'identité:</label>
                    <input type="file" id="pj" name="pj" accept=".jpg,.jpeg,.png,.pdf">
                </div>
            </div>

            <div>
                <label for="quittance" class="checkbox-container">
                    <input type="checkbox" id="quittance" name="quittance">
                    <i class="fas fa-file-alt"></i> Je souhaite une quittance mensuelle
                </label>
            </div>
            <div>
                <label for="rgpd" class="checkbox-container">
                    <input type="checkbox" id="rgpd" name="rgpd">
                    <i class="fas fa-user-shield"></i> Supprimer mes données personnelles une fois le bail terminé
                </label>
            </div>

            <div class="flex-container">
                <button type="submit"><i class="fas fa-save"></i> Soumettre</button>
                <button type="button" onclick="window.location.href='ajout_locataire.php';"><i class="fas fa-times"></i> Annuler</button>
            </div>
        </form>
    </div>
</body>
</html>
