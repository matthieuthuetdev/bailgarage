<?php
$tenant = new Tenants();

function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : '';
}

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
        $success = $tenant->update(
            $_GET["id"],
            $_SESSION['ownerId'],
            $_POST['name'],
            $_POST['firstName'],
            $_POST['company'],
            $_POST['address'],
            $_POST['additionalAddress'],
            $_POST["cityName"],
            $_POST["postalCode"],
            $_POST['phoneNumber'],
            $_POST['landlinePhoneNumber'],
            $_POST['email'],
            isset($_POST['rgpd']) ? 1 : 0,
            $_POST['attachmentPath'],
            isset($_POST['gender']) ? 1 : 0,
            isset($_POST['receipt']) ? 1 : 0,
            $_POST['ownerNote']
        );
        $message = $success ? "Locataire modifié avec succès." : "Erreur lors de la modification du locataire.";
    }
    echo "$message";
}
$tenantInfo = $tenant->read($_SESSION["ownerId"], $_GET["id"]);
?>

<h1>Modifier un locataire</h1>
<form action="" method="post">
    <div>
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" required value="<?php echo displayValue(isset($_POST['name']) ? $_POST['name'] : $tenantInfo['name']); ?>">
    </div>

    <div>
        <label for="firstName">Prénom :</label>
        <input type="text" name="firstName" id="firstName" required value="<?php echo displayValue(isset($_POST['firstName']) ? $_POST['firstName'] : $tenantInfo['firstName']); ?>">
    </div>

    <div>
        <label for="company">Entreprise :</label>
        <input type="text" name="company" id="company" value="<?php echo displayValue(isset($_POST['company']) ? $_POST['company'] : $tenantInfo['company']); ?>">
    </div>

    <div>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required value="<?php echo displayValue(isset($_POST['email']) ? $_POST['email'] : $tenantInfo['email']); ?>">
    </div>

    <div>
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" value="<?php echo displayValue(isset($_POST['address']) ? $_POST['address'] : $tenantInfo['address']); ?>">
    </div>

    <div>
        <label for="additionalAddress">Complément d'adresse :</label>
        <input type="text" name="additionalAddress" id="additionalAddress" value="<?php echo displayValue(isset($_POST['additionalAddress']) ? $_POST['additionalAddress'] : $tenantInfo['additionalAddress']); ?>">
    </div>

    <div>
        <label for="cityName">Ville :</label>
        <input type="text" name="cityName" id="cityName" value="<?php echo displayValue(isset($_POST['cityName']) ? $_POST['cityName'] : $tenantInfo["cityName"]); ?>">
    </div>

    <div>
        <label for="postalCode">Code postal :</label>
        <input type="text" name="postalCode" id="postalCode" value="<?php echo displayValue(isset($_POST['postalCode']) ? $_POST['postalCode'] : $tenantInfo["postalCode"]); ?>">
    </div>

    <div>
        <label for="phoneNumber">Téléphone :</label>
        <input type="tel" name="phoneNumber" id="phoneNumber" required value="<?php echo displayValue(isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : $tenantInfo['phoneNumber']); ?>">
    </div>

    <div>
        <label for="landlinePhoneNumber">Téléphone fixe :</label>
        <input type="tel" name="landlinePhoneNumber" id="landlinePhoneNumber" value="<?php echo displayValue(isset($_POST['landlinePhoneNumber']) ? $_POST['landlinePhoneNumber'] : $tenantInfo['landlinePhoneNumber']); ?>">
    </div>

    <div>
        <input type="checkbox" name="rgpd" id="rgpd" <?php echo isset($_POST['rgpd']) ? 'checked' : ($tenantInfo['rgpd'] ? 'checked' : ''); ?>>
        <label for="rgpd">Une fois la location terminée, je souhaite que mes données personnelles soient anonymisées.</label>
    </div>

    <div>
        <label for="attachmentPath">Pièce jointe :</label>
        <input type="text" name="attachmentPath" id="attachmentPath" value="<?php echo displayValue(isset($_POST['attachmentPath']) ? $_POST['attachmentPath'] : $tenantInfo['attachmentPath']); ?>">
    </div>

    <div>
        <label for="gender">Genre :</label>
        <select name="gender" id="gender">
            <option value="0" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 0) ? 'selected' : ($tenantInfo['gender'] == 0 ? 'selected' : ''); ?>>Homme</option>
            <option value="1" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 1) ? 'selected' : ($tenantInfo['gender'] == 1 ? 'selected' : ''); ?>>Femme</option>
        </select>
    </div>

    <div>
        <input type="checkbox" name="receipt" id="receipt" <?php echo isset($_POST['receipt']) ? 'checked' : ($tenantInfo['receipt'] ? 'checked' : ''); ?>>
        <label for="receipt">Je souhaite recevoir une quittance de loyer chaque mois.</label>
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote" rows="3"><?php echo displayValue(isset($_POST['ownerNote']) ? $_POST['ownerNote'] : $tenantInfo['ownerNote'] ?? ''); ?></textarea>
    </div>

    <button type="submit">Enregistrer</button>
</form>
