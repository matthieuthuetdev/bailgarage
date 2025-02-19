<!-- reset_password.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de Mot de Passe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="../css/connexion.css">
</head>
<body>
    <?php
    require '../includes/db.php';
    
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        echo "<div class='container'><p class='error-message'>Lien invalide.</p></div>";
    } else {
        // Vérifier si le token existe dans la base de données
        $sql = "SELECT * FROM users WHERE reset_token = :token";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Afficher le formulaire de réinitialisation de mot de passe
            echo "
            <div class='container'>
                <h2>Réinitialisation de Mot de Passe</h2>
                <form action='traitement_reset_password.php' method='post'>
                    <input type='hidden' name='token' value='$token'>
                    <div class='input-group'>
                        <i class='fas fa-lock'></i>
                        <input type='password' id='password' name='password' placeholder='Nouveau Mot de Passe' required>
                    </div>
                    <div class='input-group'>
                        <i class='fas fa-lock'></i>
                        <input type='password' id='confirm_password' name='confirm_password' placeholder='Confirmer le Mot de Passe' required>
                    </div>
                    <button type='submit' name='submit'>Réinitialiser Mot de Passe</button>
                </form>
                <div class='links'>
                    <a href='connexion.php'><i class='fas fa-sign-in-alt'></i> Se connecter</a>
                    <a href='../index.html'><i class='fas fa-home'></i> Retour à la page principale</a>
                </div>
            </div>";
        } else {
            echo "<div class='container'><p class='error-message'>Lien invalide.</p></div>";
        }
    }
    ?>
</body>
</html>
