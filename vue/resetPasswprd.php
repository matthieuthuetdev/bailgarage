<?php
$user = new Users();
$userId = $user->searchUserByResetPasswordTocken($_GET["tocken"]);
if (!empty($userId)) {
    if (!empty($_POST['password']) && $_POST['password'] !== $_POST['confirmPassword']) {
        $message = "Les mots de passe ne correspondent pas.";
    }else{

    }
} else {
    $message = "tocken invalide !";
}
