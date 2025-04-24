<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Administrateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="../css/connexion.css">
</head>
<body>
    <div class="container">
        <h2>Connexion Administrateur</h2>
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']);
        }
        ?>
        <p class="test-user">Admin de test:<br>
            email: admin@bailgarage.fr<br>
            mdp: admin
        </p>
        <form action="../includes/connexion_process.php" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <input type="hidden" name="user_role" value="admin">
            <input type="submit" name="login-submit" value="Se connecter">
        </form>
		<div class="links">
        <a href="forgot_password.php"><i class="fas fa-key"></i> Mot de passe oublié ?</a>
        <a href="../index.html"><i class="fas fa-home"></i> Retour à la page principale</a>
    </div>
    </div>
    
</body>
</html>

