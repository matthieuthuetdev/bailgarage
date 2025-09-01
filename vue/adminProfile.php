<?php
$message = "";

$userId = $_SESSION['adminId'];
$users = new Users();
$userInfo = $users->read($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['name']) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $_POST['name'])) {
        $message = "Nom invalide.";
    } elseif (empty($_POST['firstName']) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $_POST['firstName'])) {
        $message = "Prénom invalide.";
    } elseif (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide.";
    } elseif ($_POST['email'] !== $userInfo['email'] && $users->searchUserByEmail($_POST['email'])) {
        $message = "Cet email est déjà utilisé par un autre utilisateur.";
        } elseif (!empty($_POST['password']) && $_POST['password'] !== $_POST['confirmPassword']) {
            $message = "Les mots de passe ne correspondent pas.";
        } else {
        $success = $users->update(
            $userId,
            $_POST['firstName'],
            $_POST['name'],
            $_POST['email'],
            !empty($_POST['password']) ? $_POST['password'] : null
        );

        if ($success) {
            $message = "Informations mises à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour.";
        }
    }
}
?>

<h1>Votre profil</h1>
<?php echo $message; ?>
<form action="" method="post">
    <h2>Informations générales</h2>
    <div>
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? $userInfo['name']); ?>">
    </div>
    <div>
        <label for="firstName">Prénom :</label>
        <input type="text" name="firstName" id="firstName" required value="<?php echo htmlspecialchars($_POST['firstName'] ?? $userInfo['firstName']); ?>">
    </div>
    <h2>Informations de connexion</h2>
    <div>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? $userInfo['email']); ?>">
    </div>
    <div>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password">
    </div>
    <div>
        <label for="confirmPassword">Confirmer le mot de passe :</label>
        <input type="password" name="confirmPassword" id="confirmPassword">
    </div>
    <button type="submit">Envoyer</button>
</form>
