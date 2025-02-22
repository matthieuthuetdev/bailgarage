<?php
if(isset($_POST["email"],$_POST["password"])){
    var_dump($_POST);
    $user = new Users();
    $result = $user->signIn($_POST["email"],$_POST["password"]);
    var_dump($result);
}
?>

<form action="" method="POST">
    <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
    </div>
    <div class="input-group">
        <input type="password" name="password" placeholder="Mot de passe" required>
    </div>
    <input type="submit" name="login-submit" value="Se connecter">
</form>