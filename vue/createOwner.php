<?php
if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['name']) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $_POST['name'])) {
        $message = "Nom invalide.";
    } elseif (empty($_POST['firstName']) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $_POST['firstName'])) {
        $message = "Prénom invalide.";
    } elseif (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide.";
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
    } else {
        $cityName = htmlspecialchars($_POST["cityName"]);
        $postalCode = htmlspecialchars($_POST["postalCode"]);

        $owner = new Owners();
        $success = $owner->create(
            $_POST['name'],
            $_POST['firstName'],
            $_POST['email'],
            $_POST['company'],
            $_POST['address'],
            $_POST['additionalAddress'],
            $cityName,
            $postalCode,
            $_POST['phoneNumber'],
            $_POST['iban'],
            $_POST['bic'],
            $_POST['attachmentPath'],
            $_POST['gender']
        );

        if ($success !== false && !empty($_POST['additionalIbans'])) {
            $additionalIban = new AdditionalIbans();
            $ownerId = $owner->searchOwnerByEmail($_POST["email"])["id"];
            if ($ownerId) {
                $additionalIban->create(
                    $ownerId,
                    $_POST["additionalIbanName"],
                    $_POST["additionalIbans"],
                    $_POST["additionalBic"]
                );
            }
        }

        $message = $success;
    }
    echo "$message";
}
?>
<h1>Créer un propriétaire</h1>
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
        <input type="text" name="address" id="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
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
        <label for="postalCode">Code postal :</label>
        <input type="text" name="postalCode" id="postalCode" value="<?php echo isset($_POST['postalCode']) ? htmlspecialchars($_POST['postalCode']) : ""; ?>">
    </div>

    <div>
        <label for="phoneNumber">Numéro de téléphone :</label>
        <input type="tel" name="phoneNumber" id="phoneNumber" required value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>">
    </div>

    <div>
        <label for="iban">IBAN :</label>
        <input type="text" name="iban" id="iban" required value="<?php echo isset($_POST['iban']) ? htmlspecialchars($_POST['iban']) : ''; ?>">
    </div>

    <div>
        <label for="bic">BIC :</label>
        <input type="text" name="bic" id="bic" required value="<?php echo isset($_POST['bic']) ? htmlspecialchars($_POST['bic']) : ''; ?>">
    </div>
    <div>
        <label for="additionalIbans">IBAN supplémentaire :</label>
        <input type="text" name="additionalIbans" id="additionalIbans" value="<?php echo isset($_POST['additionalIbans']) ? htmlspecialchars($_POST['additionalIbans']) : ''; ?>">
    </div>
    <div>
        <label for="additionalIbanName">Nom de l'IBAN supplémentaire :</label>
        <input type="text" name="additionalIbanName" id="additionalIbanName" value="<?php echo isset($_POST['additionalIbanName']) ? htmlspecialchars($_POST['additionalIbanName']) : ''; ?>">
    </div>

    <div>
        <label for="additionalBic">BIC supplémentaire :</label>
        <input type="text" name="additionalBic" id="additionalBic" value="<?php echo isset($_POST['additionalBic']) ? htmlspecialchars($_POST['additionalBic']) : ''; ?>">
    </div>

    <div>
        <label for="attachmentPath">Pièce jointe :</label>
        <input type="text" name="attachmentPath" id="attachmentPath" value="<?php echo isset($_POST['attachmentPath']) ? htmlspecialchars($_POST['attachmentPath']) : ''; ?>">
    </div>

    <div>
        <label for="gender">Genre :</label>
        <select name="gender" id="gender" required>
            <option value="homme" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'homme') ? 'selected' : ''; ?>>Homme</option>
            <option value="femme" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'femme') ? 'selected' : ''; ?>>Femme</option>
        </select>
    </div>


    <button type="submit">Envoyer</button>
</form>