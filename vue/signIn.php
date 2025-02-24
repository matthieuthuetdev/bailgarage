<?php
if (isset($_POST["email"], $_POST["password"])) {
    $user = new Users();
    $result = $user->signIn($_POST["email"], $_POST["password"]);
    var_dump($result);
    if (!empty($result)) {
        $_SESSION["name"] = $result["nom"];
        $_SESSION["firstName"] = $result["prenom"];
        $_SESSION["right"] = $result["role"];
        if ($_SESSION["right"] == "proprietaire") {
            header("location: index.php?pageController=garage&action=display");
        }else {
            header("location: index.php?pageController=owner&action=display");
        }
    }
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