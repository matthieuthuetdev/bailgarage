<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../admin/connexion_admin.php"); // Redirige s'il n'est pas connecté en tant qu'admin
    exit();
}

// Récupérer l'ID de l'utilisateur depuis la session
$user_id = $_SESSION['user_id'];

// Récupérer le proprietaire_id correspondant à l'utilisateur connecté
$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_id = $stmt->fetchColumn();
$stmt->closeCursor();

// Vérification que le proprietaire_id a été trouvé
if (!$proprietaire_id) {
    echo "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
    exit();
}

// Vérifiez si un locataire_id est passé en paramètre
if (!isset($_GET['locataire_id'])) {
    echo "Erreur: Aucun locataire spécifié.";
    exit();
}

$locataire_id = $_GET['locataire_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        if ($_FILES['pj']['size'] > 2000000) {
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

                    $success_message = "La pièce jointe a été ajoutée avec succès.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Erreur PDO: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout de Pièce Jointe</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Reset and Global Styles */
        body, h1, p, form, input, button, div {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            color: #213c4a; /* Dark Gray/Black */
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #e9eef1; /* Light Teal background */
            padding: 20px;
        }

        /* Container Styles */
        .container {
            background: #ffffff; /* Off-White */
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #002011; /* Dark Teal */
        }

        /* Message Styles */
        .error-message {
            color: #e74c3c; /* Red */
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .success-message {
            color: #2ecc71; /* Green */
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Dropzone Styles */
        .dropzone {
            border: 2px dashed #587f96; /* Light Teal */
            border-radius: 4px;
            padding: 20px;
            cursor: pointer;
            color: #587f96; /* Light Teal */
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .dropzone i {
            font-size: 50px;
            margin-bottom: 10px;
            color: #213c4a; /* Dark Gray/Black */
        }

        .dropzone.dragover {
            background-color: #f1f7fb; /* Lighter Teal */
        }

        /* Button Styles */
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #213c4a; /* Dark Gray/Black */
            color: #ffffff; /* White */
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #002011; /* Dark Teal */
        }

        /* Responsive Styles */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .dropzone {
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-paperclip"></i> Ajout de Pièce Jointe</h1>

        <?php if (!empty($error_message)): ?>
        <p class="error-message"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
        <p class="success-message"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <div class="dropzone" id="dropzone">
            <i class="fas fa-cloud-upload-alt"></i><br>
            Glissez-déposez un fichier ici ou cliquez pour télécharger
        </div>

        <form action="ajout_pj.php?locataire_id=<?= htmlspecialchars($locataire_id); ?>" method="post" enctype="multipart/form-data">
            <input type="file" id="fileInput" name="pj" style="display: none;">
            <button type="submit"><i class="fas fa-upload"></i> Soumettre</button>
        </form>
    </div>
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');

        dropzone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                dropzone.textContent = fileInput.files[0].name;
            }
        });

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');

            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                dropzone.textContent = e.dataTransfer.files[0].name;
            }
        });
    </script>
</body>
</html>
