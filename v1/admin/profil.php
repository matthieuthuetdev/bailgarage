<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: connexion_admin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'administrateur
$sql_user = "SELECT * FROM users WHERE user_id = :user_id";
$stmt_user = $db->prepare($sql_user);
$stmt_user->bindParam(':user_id', $user_id);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Mise à jour des informations de profil via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax']) && $_POST['ajax'] == 'update_profile') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas.', 'type' => 'error']);
        exit();
    }

    try {
        $db->beginTransaction();

        $sql_update_user = "UPDATE users SET nom = :nom, prenom = :prenom, email = :email WHERE user_id = :user_id";
        $stmt_update_user = $db->prepare($sql_update_user);
        $stmt_update_user->bindParam(':nom', $nom);
        $stmt_update_user->bindParam(':prenom', $prenom);
        $stmt_update_user->bindParam(':email', $email);
        $stmt_update_user->bindParam(':user_id', $user_id);
        $stmt_update_user->execute();

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_update_password = "UPDATE users SET password = :password WHERE user_id = :user_id";
            $stmt_update_password = $db->prepare($sql_update_password);
            $stmt_update_password->bindParam(':password', $hashed_password);
            $stmt_update_password->bindParam(':user_id', $user_id);
            $stmt_update_password->execute();
        }

        $db->commit();

        echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès.', 'type' => 'success']);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du profil.', 'type' => 'error']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Administrateur</title>
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div id="message" class=""></div>

    <div class="container">
        <nav>
            <ul> 
                <li>
                    <a href="admin_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a>
                </li>
                <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>  
            </ul>
        </nav><br>

        <h2>Informations Administrateur</h2>
        <form id="updateForm" action="profil.php" method="POST">
            <div class="flex-container">
                <div>
                    <label for="nom"><i class="fas fa-user"></i> Nom:</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>">
                </div>

                <div>
                    <label for="prenom"><i class="fas fa-user"></i> Prénom:</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>">
                </div>
            </div>

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

            <input type="hidden" name="ajax" value="update_profile">
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
                document.getElementById('email').value = formData.get('email');
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
    </script>
</body>
</html>
