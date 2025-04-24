<?php
include_once 'db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Verify the token
    $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bindParam(1, $token, PDO::PARAM_STR);
    $stmt->execute();
    $email = $stmt->fetchColumn();

    if ($email) {
        // Update the user's password in the database
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bindParam(1, $newPassword, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Your password has been successfully reset.";
            // Optionally delete the token after use
            $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bindParam(1, $token, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            echo "Failed to reset password.";
        }
    } else {
        echo "Invalid token.";
    }
}
?>
