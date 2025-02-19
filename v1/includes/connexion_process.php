<?php 
ini_set('display_errors', 0); // Désactive l'affichage des erreurs
ini_set('log_errors', 1); // Active l'enregistrement des erreurs
ini_set('error_log', '../../logs/app.bailgarage.fr/error_log'); // Chemin vers le fichier de log des erreurs

include_once 'db.php'; 
session_start();

if (isset($_POST['login-submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_role = $_POST['user_role'];
    
    // Prépare la requête SQL en fonction du type d'utilisateur
    if ($user_role == 'admin') {
        $sql = "SELECT * FROM users WHERE email = :email AND role = 'admin'";
    } else {
        $sql = "SELECT * FROM users WHERE email = :email AND role = 'proprietaire'";
    }

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifie si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($password, $user['password'])) {
        // Régénère l'ID de session pour sécuriser la session après l'authentification
        session_regenerate_id();

        // Stocke les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Redirige en fonction du type d'utilisateur
        if ($_SESSION['user_role'] == 'admin') {
            header("Location: ../admin/admin_dash.php");
        } else {
            header("Location: ../proprietaire/proprietaire_dash.php");
        }
        exit();
    } else {
        // Stocke un message d'erreur dans la session
        $_SESSION['error_message'] = "Email ou mot de passe incorrect.";
        // Redirige vers la page de connexion appropriée
        if ($user_role == 'admin') {
            header("Location: ../admin/connexion_admin.php");
        } else {
            header("Location: ../proprietaire/connexion_proprietaire.php");
        }
        exit();
    }
} else {
    // Stocke un message d'erreur si le formulaire n'a pas été soumis
    $_SESSION['error_message'] = "Formulaire non soumis.";
    // Redirige vers la page de connexion appropriée
    if (isset($_POST['user_role']) && $_POST['user_role'] == 'admin') {
        header("Location: ../admin/connexion_admin.php");
    } else {
        header("Location: ../proprietaire/connexion_proprietaire.php");
    }
    exit();
}
?>
