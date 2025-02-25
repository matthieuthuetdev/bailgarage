<?php
if (isset($_POST["email"], $_POST["password"])) {
    $user = new Users();
    $result = $user->signIn($_POST["email"], $_POST["password"]);
    if (!empty($result)) {
        $_SESSION["name"] = $result["nom"];
        $_SESSION["firstName"] = $result["prenom"];
        $_SESSION["right"] = $result["role"];
        if ($_SESSION["right"] == "proprietaire") {
            $_SESSION["message"] = "<span>Bienvenue " . $_SESSION["firstName"] . " vous êtes bien connecter ent tant que propriétaire.</span>";
            header("location:index.php?pageController=garage&action=display");
        } else {
            $_SESSION["message"] = "<span>Bienvenue " . $_SESSION["firstName"] . " vous êtes bien connecter ent tant qu'administrateur.</span>";
            header("location: index.php?pageController=user&action=display");
        }
    } else {
        echo "adresse email ou le mot de passe incorect.";
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