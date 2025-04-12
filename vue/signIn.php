<?php
if (isset($_POST["email"], $_POST["password"])) {
    $user = new Users();
    $result = $user->signIn($_POST["email"], $_POST["password"]);
    if (!empty($result)) {
        $_SESSION["name"] = $result["name"];
        $_SESSION["firstName"] = $result["firstName"];
        if ($result["roleName"] == "owner") {
            $_SESSION["role"] = "owner";
            $_SESSION["message"] = "<span>Bienvenue " . $_SESSION["firstName"] . " vous êtes bien connecter en tant que propriétaire.</span>";
            $owner = new Owners();
            $ownerId = $owner->searchOwnerByUserId($result["id"]);
            $_SESSION["ownerId"] = $ownerId["id"];
            header("location:index.php?pageController=garage&action=display");
        } else {
            $_SESSION["role"] = "admin";
            $_SESSION["message"] = "<span>Bienvenue " . $_SESSION["firstName"] . " vous êtes bien connecter en tant qu'administrateur.</span>";
            var_dump($_SESSION);
            // header("location: index.php?pageController=owner&action=display");
        }
    } else {
        echo "adresse email ou le mot de passe incorect.";
    }
}

?>
<script src="./js/signIn.js" type="module"></script>
<form action="" method="POST">
    <div class="input-group">
        <input type="email" name="email" placeholder="Email" id="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""; ?>" required>
    </div>
    <div class="input-group">
        <input type="password" name="password" placeholder="Mot de passe" id="password" required>
    </div>
    <input type="submit" name="login-submit" value="Se connecter">
</form>
<div>
    <button id="btnAdmin">Admin</button>
</div>
<div>
    <button id="btnOwner">Propriétaire</button>
</div>