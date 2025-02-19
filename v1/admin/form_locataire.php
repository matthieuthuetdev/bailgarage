<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Assurez-vous que la connexion à la base de données est correctement incluse
include_once '../includes/db.php';

$success_message = "";
$error_message = "";

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
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $addresse = $_POST['addresse']; 
    $complement = $_POST['complement'];
    $cp = $_POST['cp'];
    $ville = $_POST['ville'];
    $telephone = $_POST['telephone'];
    $fixe = $_POST['fixe'];
    $email = $_POST['email'];
    $rgpd = isset($_POST['rgpd']) ? 1 : 0;
    $quittance = isset($_POST['quittance']) ? 1 : 0;
    $genre = $_POST['genre'];

    $piece_jointe_path = null;
    $piece_jointe_nom = null;

    // Vérifier si un fichier a été téléchargé
    if (isset($_FILES['pj']) && $_FILES['pj']['error'] == UPLOAD_ERR_OK) {
        $piece_jointe_tmp = $_FILES['pj']['tmp_name'];
        $piece_jointe_nom = $_FILES['pj']['name'];
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

        // Vérifier le type de fichier (optionnel)
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
                $stmt->bindParam(1, $proprietaire_id, PDO::PARAM_INT);
                $stmt->bindParam(2, $nom, PDO::PARAM_STR);
                $stmt->bindParam(3, $prenom, PDO::PARAM_STR);
                $stmt->bindParam(4, $addresse, PDO::PARAM_STR); 
                $stmt->bindParam(5, $complement, PDO::PARAM_STR);
                $stmt->bindParam(6, $cp, PDO::PARAM_STR);
                $stmt->bindParam(7, $ville, PDO::PARAM_STR);
                $stmt->bindParam(8, $telephone, PDO::PARAM_STR);
                $stmt->bindParam(9, $fixe, PDO::PARAM_STR);
                $stmt->bindParam(10, $email, PDO::PARAM_STR);
                $stmt->bindParam(11, $rgpd, PDO::PARAM_INT);
                $stmt->bindParam(12, $quittance, PDO::PARAM_INT);
                $stmt->bindParam(13, $genre, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    // Récupérer l'ID du locataire nouvellement inséré
                    $locataire_id = $db->lastInsertId();

                    // Ajouter l'ID du locataire au nom du fichier pour garantir l'unicité
                    if ($piece_jointe_nom) {
                        $piece_jointe_nom = $locataire_id . '_' . $piece_jointe_nom;
                        $piece_jointe_path = $upload_dir . basename($piece_jointe_nom);

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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        h1 {
            color: #555;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #333;
        }

        .error-message {
            color: red;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
		
		.success-message {
            color: green;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
		
		.add-attachment {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            border: 2px dashed #ccc;
            border-radius: 4px;
            color: #ccc;
            font-size: 24px;
            cursor: pointer;
            margin-top: 20px;
            transition: border-color 0.3s, color 0.3s;
        }

        .add-attachment:hover {
            border-color: #587f96;
            color: #587f96;
        }
		
    </style>
</head>
<body>
    <h1>Formulaire de Locataire</h1>
    
	
	<?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>
	
    <form action="form_locataire.php" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Informations du Locataire</legend>
			
			<label for="genre">Genre:</label>
			<div>
    			<input type="radio" id="genre_m" name="genre" value="M">
    			<label for="genre_m" style="font-weight: normal; display: inline;">M</label>
    			<input type="radio" id="genre_mme" name="genre" value="MME">
    			<label for="genre_mme" style="font-weight: normal; display: inline;">MME</label>
			</div> <br>
			
            <label for="nom">Nom*:</label>
            <input type="text" id="nom" name="nom" required><br>

            <label for="prenom">Prénom*:</label>
            <input type="text" id="prenom" name="prenom" required><br>

            <label for="addresse">Adresse*:</label>
            <input type="text" id="addresse" name="addresse" required><br>
			
			<label for="complement">Complément:</label>
            <input type="text" id="complement" name="complement"><br>

            <label for="cp">Code Postal*:</label>
            <input type="text" id="cp" name="cp" required><br>

            <label for="ville">Ville*:</label>
            <input type="text" id="ville" name="ville" required><br>

            <label for="telephone">Téléphone mobile*:</label>
            <input type="tel" id="telephone" name="telephone" required><br>
			
			<label for="fixe">Téléphone fixe:</label>
            <input type="tel" id="fixe" name="fixe"><br>

            <label for="email">Email*:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="pj">Pièce d'identité:</label>
            <input type="file" id="pj" name="pj" accept=".jpg,.jpeg,.png,.pdf"><br>
			
        </fieldset>

        <fieldset>
            <legend>Quittance</legend>
            <label for="quittance">
                <input type="checkbox" id="quittance" name="quittance">
                Je souhaite une quittance mensuelle
            </label><br>
            
        </fieldset>

        <fieldset>
            <legend>RGPD</legend>
            <label for="rgpd">
                <input type="checkbox" id="rgpd" name="rgpd">
                Supprimer mes données personnelles une fois le bail terminé (automatiquement 18 mois après)
            </label><br><br>
        </fieldset>

        <button type="submit">Soumettre</button>
		<button type="button" onclick="window.close();">Annuler</button>

    </form>
</body>
</html>

