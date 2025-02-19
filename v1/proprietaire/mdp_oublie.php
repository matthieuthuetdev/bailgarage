<?php
session_start();
include_once '../includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$message_class = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    try {
        // Vérifier si l'email existe dans la base de données
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Générer un token pour la réinitialisation de mot de passe
            $token = bin2hex(random_bytes(32));

            // Mettre à jour le token dans la base de données pour cet utilisateur
            $sql_update_token = "UPDATE users SET reset_token = :token WHERE user_id = :user_id";
            $stmt_update_token = $db->prepare($sql_update_token);
            $stmt_update_token->bindParam(':token', $token);
            $stmt_update_token->bindParam(':user_id', $user['user_id']);
            $stmt_update_token->execute();

            // Envoyer l'email de réinitialisation
            $reset_link = "https://app.bailgarage.fr/proprietaire/reset_password.php?token=" . $token;
            $mail_body = "Bonjour, <br><br> Pour réinitialiser votre mot de passe, cliquez sur le lien suivant : <a href='$reset_link'>$reset_link</a>";
            
            require '../includes/phpmailer/src/Exception.php';
            require '../includes/phpmailer/src/PHPMailer.php';
            require '../includes/phpmailer/src/SMTP.php';

            $mail = new PHPMailer(true);
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lililizalopi2021@gmail.com';
            $mail->Password = 'esbb phvk adqu zzgm';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Destinataire
            $mail->setFrom('lililizalopi2021@gmail.com', 'Admin BailGarage');
            $mail->addAddress($email);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Nouveau Mot de Passe';
            $mail->Body = $mail_body;

            // Envoyer l'email
            $mail->send();

            $_SESSION['message'] = "Email de réinitialisation envoyé à votre adresse email.";
            $_SESSION['message_class'] = 'success-message';
        } else {
            $_SESSION['message'] = "Aucun compte trouvé avec cette adresse email.";
            $_SESSION['message_class'] = 'error-message';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Erreur lors de l'envoi de l'email : " . $e->getMessage();
        $_SESSION['message_class'] = 'error-message';
        error_log($e->getMessage());
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Affichage des messages de session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_class = $_SESSION['message_class'];
    unset($_SESSION['message']);
    unset($_SESSION['message_class']);
} else {
    $message = "";
    $message_class = "";
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de Passe Oublié</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="../css/connexion.css">
</head>
<body>
    <div class="container">
        <h2>Mot de Passe Oublié</h2>
        <?php if (!empty($message)) : ?>
            <p class="<?php echo $message_class; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Adresse Email" required>
            </div>
            <button type="submit" name="submit">Envoyer Email de Réinitialisation</button>
        </form>
		<div class="links">
        <a href="connexion_proprietaire.php"><i class="fas fa-sign-in-alt"></i> Retour à la Connexion</a>
        <a href="../index.html"><i class="fas fa-home"></i> Retour à la page principale</a>
    </div>
    </div>
    
</body>
</html>
