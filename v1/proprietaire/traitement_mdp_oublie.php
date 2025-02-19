<?php
session_start();
include_once '../includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
            $reset_link = "https://app.bailgarage.fr/pages%20propri/reset_password.php?token=" . $token;
            $mail_body = "Pour réinitialiser votre mot de passe, cliquez sur le lien suivant : <a href='$reset_link'>$reset_link</a>";
            
            require '../includes/phpmailer/src/Exception.php';
            require '../includes/phpmailer/src/PHPMailer.php';
            require '../includes/phpmailer/src/SMTP.php';

            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;

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
            $mail->setFrom('lililizalopi2021@gmail.com');
            $mail->addAddress($email);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de Mot de Passe';
            $mail->Body = $mail_body;

            // Envoyer l'email
            $mail->send();

            $message = "Email de réinitialisation envoyé à votre adresse email.";
        } else {
            $message = "Aucun compte trouvé avec cette adresse email.";
        }
    } catch (Exception $e) {
        $message = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
    }
} else {
    $message = "Erreur : Veuillez fournir une adresse email.";
}

echo $message;
?>
