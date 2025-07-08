<?php
if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['name'])) {
        $message = "Le nom est obligatoire.";
    } elseif (empty($_POST['firstName'])) {
        $message = "Le prénom est obligatoire.";
    } elseif (empty($_POST['email'])) {
        $message = "L'email est obligatoire.";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Format d'email invalide.";
    } elseif (empty($_POST['phoneNumber'])) {
        $message = "Le numéro de téléphone est obligatoire.";
    } else {
        $tenant = new Tenants();
        $success = $tenant->create(
            $_SESSION['ownerId'],
            $_POST['name'],
            $_POST['firstName'],
            $_POST['company'],
            $_POST['address'],
            $_POST['additionalAddress'],
            1,
            $_POST["cityName"],
            $_POST["postalCode"],
            $_POST["country"],
            $_POST['phoneNumber'],
            $_POST['landlinePhoneNumber'],
            $_POST['email'],
            isset($_POST['rgpd']) ? 1 : 0,
            isset($_POST['gender']) ? 1 : 0,
            isset($_POST['receipt']) ? 1 : 0,
            $_POST['ownerNote']
        );
        $message = $success ? "Locataire créé avec succès." : "Erreur lors de la création du locataire.";
    }
    echo "$message";
}
?>
<h1>Créer un locataire</h1>
<form action="" method="post">
    <div>
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
    </div>

    <div>
        <label for="firstName">Prénom :</label>
        <input type="text" name="firstName" id="firstName" required value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
    </div>
    <div>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
    </div>
    <div>
        <label for="company">Entreprise :</label>
        <input type="text" name="company" id="company" value="<?php echo isset($_POST['company']) ? htmlspecialchars($_POST['company']) : ''; ?>">
    </div>

    <div>
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
    </div>

    <div>
        <label for="additionalAddress">Complément d'adresse :</label>
        <input type="text" name="additionalAddress" id="additionalAddress" value="<?php echo isset($_POST['additionalAddress']) ? htmlspecialchars($_POST['additionalAddress']) : ''; ?>">
    </div>
    <div>
        <label for="cityName">Ville :</label>
        <input type="text" name="cityName" id="cityName" value="<?php echo isset($_POST['cityName']) ? htmlspecialchars($_POST['cityName']) : ""; ?>">
    </div>
    <div>
        <label for="postalCode">Code postale :</label>
        <input type="text" name="postalCode" id="postalCode" value="<?php echo isset($_POST['postalCode']) ? htmlspecialchars($_POST['postalCode']) :""; ?>">
    </div>
    <div>
        <label for="country">Pays :</label>
        <input type="text" name="country" id="country" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : 'France'; ?>">
    </div>

    <div>
        <label for="phoneNumber">Téléphone :</label>
        <input type="tel" name="phoneNumber" id="phoneNumber" required value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>">
    </div>

    <div>
        <label for="landlinePhoneNumber">Téléphone fixe :</label>
        <input type="tel" name="landlinePhoneNumber" id="landlinePhoneNumber" value="<?php echo isset($_POST['landlinePhoneNumber']) ? htmlspecialchars($_POST['landlinePhoneNumber']) : ''; ?>">
    </div>

    <div>
        <input type="checkbox" name="rgpd" id="rgpd" <?php echo isset($_POST['rgpd']) ? 'checked' : ''; ?>>
        <label for="rgpd">Une foie la location je souhaite que mes données personnelle soit Anonymiser. :</label>
    </div>

    <div>
        <label for="attachmentPath">Pièce jointe :</label>
        <input type="text" name="attachmentPath" id="attachmentPath" value="<?php echo isset($_POST['attachmentPath']) ? htmlspecialchars($_POST['attachmentPath']) : ''; ?>">
    </div>

    <div>
        <label for="gender">Genre :</label>
        <select name="gender" id="gender">
            <option value="0" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 0) ? 'selected' : ''; ?>>Homme</option>
            <option value="1" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 1) ? 'selected' : ''; ?>>Femme</option>
        </select>
    </div>

    <div>
        <input type="checkbox" name="receipt" id="receipt" <?php echo isset($_POST['receipt']) ? 'checked' : ''; ?>>
        <label for="receipt">je souhaite recevoir une quittancer de loyer chaque mois</label>
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote" rows="3"><?php echo isset($_POST['ownerNote']) ? htmlspecialchars($_POST['ownerNote']) : ''; ?></textarea>
    </div>

    <button type="submit">Enregistrer</button>
</form>
