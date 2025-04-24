<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/db.php';
session_start();

// ID du bail
$bail_id = $_GET['id'] ?? null;
if (!$bail_id) {
    echo "Bail ID manquant.";
    exit();
}

// Récupérer les informations du bail
$stmt = $db->prepare("SELECT * FROM baux WHERE bail_id = ?");
$stmt->bindParam(1, $bail_id, PDO::PARAM_INT);
$stmt->execute();
$bail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bail) {
    echo "Bail non trouvé.";
    exit();
}

$message = ''; // Déclaration de la variable $message en dehors du bloc if

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdf_path = null;
    $pdf_nom = null;
    $pdf_type = null;

    // Vérifier si un fichier a été téléchargé
    if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] == UPLOAD_ERR_OK) {
        $pdf_tmp = $_FILES['piece_jointe']['tmp_name'];
        $pdf_nom = $_FILES['piece_jointe']['name'];
        $pdf_type = $_FILES['piece_jointe']['type'];

        // Définir le chemin où le fichier sera stocké
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $pdf_path = $upload_dir . basename($pdf_nom);

        // Vérifier la taille du fichier
        if ($_FILES['piece_jointe']['size'] > 2000000) { // Limite de taille à 2MB
            $message = "Erreur: La taille du fichier dépasse la limite autorisée de 2MB.";
        }

        // Vérifier le type de fichier (optionnel)
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($pdf_type, $allowed_types)) {
            $message = "Erreur: Seuls les fichiers JPG, PNG et PDF sont autorisés.";
        }

        // Déplacer le fichier téléchargé vers le répertoire souhaité
        if (empty($message) && !move_uploaded_file($pdf_tmp, $pdf_path)) {
            $message = "Erreur: Impossible de déplacer le fichier téléchargé.";
        }
    }

    if (empty($message)) {
        try {
            // Requête avec le chemin de la pièce jointe
            $stmt = $db->prepare("UPDATE baux SET pdf_path = ?, pdf_name = ?, pdf_type = ? WHERE bail_id = ?");
            $stmt->bindParam(1, $pdf_path, PDO::PARAM_STR);
            $stmt->bindParam(2, $pdf_nom, PDO::PARAM_STR);
            $stmt->bindParam(3, $pdf_type, PDO::PARAM_STR);
            $stmt->bindParam(4, $bail_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $message = "Le fichier a été téléchargé et les informations ont été mises à jour avec succès.";
            } else {
                $message = "Erreur: " . $stmt->errorInfo()[2];
            }
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
    <title>Importer le bail</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styles for the body */
        body {
            font-family: Arial, sans-serif;
            background-color: #e9eef1; /* Light grayish blue */
            color: #213c4a; /* Dark gray/black */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container styles */
        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 600px;
        }

        .container {
            background-color: #ffffff; /* White */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        /* Nav styles */
        nav {
            width: 100%;
            margin-bottom: 20px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: space-around;
            background-color: #213c4a; /* Dark Gray/Black */
            border-radius: 4px;
        }

        nav ul li {
            position: relative;
        }

        nav ul li a {
            color: #ffffff; /* White */
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        nav ul li a:hover {
            background-color: #002011; /* Dark Teal */
        }

        nav ul li ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #213c4a;
            border-radius: 4px;
            list-style-type: none;
            padding: 0;
            margin: 0;
            z-index: 1000;
        }

        nav ul li:hover ul {
            display: block;
        }

        nav ul li ul li {
            width: 100%;
        }

        nav ul li ul li a {
            padding: 10px 20px;
        }

        /* Heading styles */
        h2 {
            color: #213c4a; /* Dark gray/black */
        }

        /* General label and input styles */
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
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

        /* Button styles */
        button {
            background-color: #213c4a; /* Dark gray/black */
            color: #ffffff; /* White */
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #002011; /* Dark teal */
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            color: #ffffff;
            background-color: #ff4d4d; /* Red */
        }

        .message.success {
            background-color: #4CAF50; /* Green */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav>
            <ul>
                <li><a href="proprietaire_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>
<?php if (!empty($message)): ?>
                <p class="message <?= strpos($message, 'succès') !== false ? 'success' : '' ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
		<br>
        <div class="container">
            <h2>Importez le bail signé ci-dessous</h2>
            <form action="import.php?id=<?= htmlspecialchars($bail_id) ?>" method="post" enctype="multipart/form-data">
                <label for="piece_jointe">Pièce Jointe:</label>
                <input type="file" id="piece_jointe" name="piece_jointe"><br><br>
                <button type="submit" name="submit"><i class="fas fa-upload"></i> Envoyer</button>
            </form>
            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var message = document.querySelector('.message');
            if (message) {
                setTimeout(function() {
                    message.style.display = 'none';
                }, 5000); // Masquer le message après 5 secondes
            }
        });
    </script>
</body>
</html>
