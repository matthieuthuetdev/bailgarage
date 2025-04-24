<?php
session_start();
include_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header("Location: reset_password.php?token=$token");
        exit();
    } else {
        // Hasher le nouveau mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Mettre à jour le mot de passe dans la base de données
        $sql_update_password = "UPDATE users SET password = :password, reset_token = NULL WHERE reset_token = :token";
        $stmt_update_password = $db->prepare($sql_update_password);
        $stmt_update_password->bindParam(':password', $hashed_password);
        $stmt_update_password->bindParam(':token', $token);

        if ($stmt_update_password->execute()) {
            header("Location: success_reset.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la réinitialisation du mot de passe.";
            header("Location: reset_password.php?token=$token");
            exit();
        }
    }
} else {
    $_SESSION['error'] = "Erreur : Veuillez fournir un token valide.";
    header("Location: reset_password.php?token=$token");
    exit();
}
?>
