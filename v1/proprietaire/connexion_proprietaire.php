<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/db.php'; 
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion propriétaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="../css/connexion.css">
</head>
<body>
    <div class="container">
        <h2>Connexion propriétaire</h2>
        <p class="test-user">User de test:<br>
            email: proprio@bailgarage.fr<br>
            mdp: 8b3ee2cb
        </p>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        ?>
        <form action="../includes/connexion_process.php" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
			
			
            <input type="submit" name="login-submit" value="Se connecter">
            <input type="hidden" name="user_role" value="proprietaire">
        </form>
		<div class="links">
        <a href="mdp_oublie.php"><i class="fas fa-key"></i> Mot de passe oublié ?</a>
        <a href="../index.html"><i class="fas fa-home"></i> Retour à la page principale</a>
    </div>
    </div>
    
</body>
</html>
