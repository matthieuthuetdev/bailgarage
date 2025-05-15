<?php
$message = "";
if (!empty($_POST["email"])) {
    if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $user = new Users;
        $userId = $user->searchUserByEmail($_POST["email"])["id"];
        if (!empty($userId)) {
            $resetToken = "";
            for ($i = 0; $i < 32; $i++) {
                $resetToken .= mt_rand(0, 9);
            }
            $user->activeResetPasswordToken($userId, $resetToken);
            $userFirstName = $user->read($userId)["firstName"];
            $mail = new MailService();
            $mail->sendTemplate($_POST["email"], "requestResetPassword", array("firstName" => $userFirstName, "token" => strval($resetToken)));
        }
        $message = "Si vous avez un compte sur le site Vous recevrez un mail pour définire un nouveau mot de passe.";
    } else {
        $message = "l'email invalide.";
    }
}
echo $message;
?>
<h1>Mot de passe oublié ?</h1>
<h2>Saisissez adresse emailavec laquelle vous vous connectez habituellement sur le site.</h2>
<form method="post" action="">
    <label for="email">Adresse email</label>
    <input type="email" name="email" id="email">
    <button>Envoyer</button>
</form>