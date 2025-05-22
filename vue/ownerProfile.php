<?php
$message = "";

$ownerId = $_SESSION['ownerId'];
$owner = new Owners();
$ownerInfo = $owner->read($ownerId);
$users = new Users();

if (!empty($_POST)) {
    if (empty($_POST['name']) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $_POST['name'])) {
        $message = "Nom invalide.";
    } elseif (empty($_POST['firstName']) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $_POST['firstName'])) {
        $message = "Prénom invalide.";
    } elseif (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide.";
    } elseif ($_POST['email'] !== $ownerInfo['email'] && $users->searchUserByEmail($_POST['email'])) {
        $message = "Cet email est déjà utilisé par un autre utilisateur.";
    } elseif (empty($_POST['address'])) {
        $message = "L'adresse est obligatoire.";
    } elseif (empty($_POST['phoneNumber']) || !preg_match("/^\\+?[0-9\\s\\-\\(\\)]+$/", $_POST['phoneNumber'])) {
        $message = "Numéro de téléphone invalide.";
    } elseif (empty($_POST['iban']) || !preg_match("/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/", $_POST['iban'])) {
        $message = "IBAN invalide.";
    } elseif (empty($_POST['bic']) || !preg_match("/^[A-Z0-9]{8,11}$/", $_POST['bic'])) {
        $message = "BIC invalide.";
    } elseif (empty($_POST['gender']) || !in_array($_POST['gender'], ["homme", "femme"])) {
        $message = "Genre invalide.";
    } elseif (!empty($_POST['password']) && $_POST['password'] !== $_POST['confirmPassword']) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $company = !empty($_POST['company']) ? $_POST['company'] : $ownerInfo['company'] ?? '';
        $additionalAddress = !empty($_POST['additionalAddress']) ? $_POST['additionalAddress'] : $ownerInfo['additionalAddress'] ?? '';
        $cityName = !empty($_POST['cityName']) ? $_POST['cityName'] : $ownerInfo['cityName'] ?? '';
        $postalCode = !empty($_POST['postalCode']) ? $_POST['postalCode'] : $ownerInfo['postalCode'] ?? '';
        $attachmentId = !empty($_POST['attachmentId']) ? $_POST['attachmentId'] : $ownerInfo['attachmentId'] ?? '';

        $success = $owner->update(
            $ownerId,
            $_POST['name'],
            $_POST['firstName'],
            $_POST['email'],
            $company,
            $_POST['address'],
            $additionalAddress,
            $cityName,
            $postalCode,
            $_POST['phoneNumber'],
            $_POST['iban'],
            $_POST['bic'],
            $attachmentId,
            $_POST['gender'],
            !empty($_POST["password"]) ? $_POST["password"] : null
        );
    }
    $message = "Informations mises à jour avec succès.";
}


?>

<h1>Votre profil</h1>
<?php echo $message; ?>
<form action="" method="post">
    <h2>Informations générales</h2>
    <div>
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? $ownerInfo['name']); ?>">
    </div>
    <div>
        <label for="firstName">Prénom :</label>
        <input type="text" name="firstName" id="firstName" required value="<?php echo htmlspecialchars($_POST['firstName'] ?? $ownerInfo['firstName']); ?>">
    </div>
    <div>
        <label for="company">Entreprise :</label>
        <input type="text" name="company" id="company" value="<?php echo htmlspecialchars($_POST['company'] ?? $ownerInfo['company'] ?? ''); ?>">
    </div>
    <div>
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" required value="<?php echo htmlspecialchars($_POST['address'] ?? $ownerInfo['address']); ?>">
    </div>
    <div>
        <label for="additionalAddress">Complément d'adresse :</label>
        <input type="text" name="additionalAddress" id="additionalAddress" value="<?php echo htmlspecialchars($_POST['additionalAddress'] ?? $ownerInfo['additionalAddress'] ?? ''); ?>">
    </div>
    <div>
        <label for="cityName">Ville :</label>
        <input type="text" name="cityName" id="cityName" value="<?php echo htmlspecialchars($_POST['cityName'] ?? $ownerInfo['cityName'] ?? ''); ?>">
    </div>
    <div>
        <label for="postalCode">Code postal :</label>
        <input type="text" name="postalCode" id="postalCode" value="<?php echo htmlspecialchars($_POST['postalCode'] ?? $ownerInfo['postalCode'] ?? ''); ?>">
    </div>
    <div>
        <label for="phoneNumber">Numéro de téléphone :</label>
        <input type="tel" name="phoneNumber" id="phoneNumber" required value="<?php echo htmlspecialchars($_POST['phoneNumber'] ?? $ownerInfo['phoneNumber']); ?>">
    </div>
    <div>
        <label for="iban">IBAN :</label>
        <input type="text" name="iban" id="iban" required value="<?php echo htmlspecialchars($_POST['iban'] ?? $ownerInfo['iban']); ?>">
    </div>
    <div>
        <label for="bic">BIC :</label>
        <input type="text" name="bic" id="bic" required value="<?php echo htmlspecialchars($_POST['bic'] ?? $ownerInfo['bic']); ?>">
    </div>
    <div>
        <label for="attachmentId">Pièce jointe :</label>
        <input type="text" name="attachmentId" id="attachmentId" value="<?php echo htmlspecialchars($_POST['attachmentId'] ?? $ownerInfo['attachmentId'] ?? ''); ?>">
    </div>
    <div>
        <label for="gender">Genre :</label>
        <select name="gender" id="gender" required>
            <option value="homme" <?php echo (($_POST['gender'] ?? $ownerInfo['gender']) === 'homme') ? 'selected' : ''; ?>>Homme</option>
            <option value="femme" <?php echo (($_POST['gender'] ?? $ownerInfo['gender']) === 'femme') ? 'selected' : ''; ?>>Femme</option>
        </select>
    </div>
    <h2>Informations de connexion</h2>
    <div>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? $ownerInfo['email']); ?>">
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