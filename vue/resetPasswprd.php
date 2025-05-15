<?php
$user = new Users();
if ($user->searchUserByResetPasswordToken($_GET["token"]) == false) {
    die("Token invalide.");
}
$userId = $user->searchUserByResetPasswordToken($_GET["token"])["id"];
$message = "";
if (!empty($_POST)) {
    if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide.";
    } elseif (empty($_POST['password']) || $_POST['password'] !== $_POST['confirmPassword']) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif ($userId != $user->searchUserByEmail($_POST["email"])["id"]) {
        $message = "L'email saisine correspond pasà l'utilisateur qui a effectuer la demande de réinicialisation de mot de passe/";
    } else {
        $password = password_hash($_POST["password"], PASSWORD_ARGON2I);
        $user->resetPassword($userId, $password);
        $user->disableResetPasswordToken($userId);
        $message = "Votre mot de passe a été modifié avec Succès ! <a href='index.php'>Se connecter</a>";
    }
    echo $message;
}
?>
<h1>modifier votre mot de passe</h1>
<form method="post" action="">
    <div>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required value="<?php echo !empty($_POST["email"]) ? $_POST["email"] : "" ?>">
    </div>
    <div>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" require >
    </div>
    <div>
        <label for="confirmPassword">Confirmer le mot de passe :</label>
        <input type="password" name="confirmPassword" id="confirmPassword">
    </div>
    <button type="submit">Envoyer</button>

</form>