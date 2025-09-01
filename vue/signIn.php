<?php
$errorMessage = "";

if (isset($_POST["email"], $_POST["password"])) {
    $user = new Users();
    $result = $user->signIn($_POST["email"], $_POST["password"]);
    
    if (!empty($result)) {
        $_SESSION["name"] = $result["name"];
        $_SESSION["firstName"] = $result["firstName"];

        if ($result["roleName"] == "owner") {
            $_SESSION["role"] = "owner";
            $_SESSION["message"] = "<<h1>Bienvenue <span class='proprio'>".$_SESSION["firstName"]."</span>, vous êtes bien connecté en tant que propriétaire.</h1>";
            $owner = new Owners();
            $ownerId = $owner->searchOwnerByUserId($result["id"]);
            $_SESSION["ownerId"] = $ownerId["id"];
            header("location: index.php?pageController=garage&action=display");
            exit;
        } else {
            $_SESSION["role"] = "admin";
            $_SESSION["message"] = "<span>Bienvenue " . $_SESSION["firstName"] . " vous êtes bien connecté en tant qu'administrateur.</span>";
            $_SESSION["adminId"] = $result["id"];
            header("location: index.php?pageController=owner&action=display");
            exit;
        }
    } else {
        $errorMessage = "Adresse email ou mot de passe incorrect.";
    }
}
?>

<style>
    @import url('./css/signin.css');
</style>

<script src="./js/signIn.js" type="module" defer></script>

<div class="left"></div>

<div class="right">
    <img src="./img/logo-bleu.png" alt="Logo"> 

    <?php if (!empty($errorMessage)): ?>
        <p style="color:red;"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div>
            <input 
                type="email" 
                name="email" 
                placeholder="Email" 
                id="email" 
                value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>" 
                required>
        </div>
        <div>
            <input 
                type="password" 
                name="password" 
                placeholder="Mot de passe" 
                id="password" 
                required>
        </div>
        <input type="submit" name="login-submit" value="Se connecter">
    </form>

    <a href="index.php?pageController=user&action=requestresetpassword">Mot de passe oublié ?</a>

    <div>
        <button id="btnAdmin">Admin</button>
    </div>
    <div>
        <button id="btnOwner">Propriétaire</button>
    </div>
</div>
