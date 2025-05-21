<?php
$garage = new Garages();

if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['address'])) {
        $message = "L'adresse est obligatoire.";
    } elseif (empty($_POST['country'])) {
        $message = "Le pays est obligatoire.";
    } elseif (empty($_POST['garageNumber']) || !is_numeric($_POST['garageNumber'])) {
        $message = "Le numéro de garage est obligatoire.";
    } elseif (empty($_POST['rentWithoutCharges']) || !is_numeric($_POST['rentWithoutCharges'])) {
        $message = "Le loyer hors charges est obligatoire.";
    } elseif (empty($_POST['charges']) || !is_numeric($_POST['charges'])) {
        $message = "Les charges sont obligatoires.";
    } elseif (empty($_POST['surface']) || !is_numeric($_POST['surface'])) {
        $message = "La surface est obligatoire.";
    } else {
        $success = $garage->create(
            $_SESSION['ownerId'],
            $_POST['address'],
            $_POST['additionalAddress'],
            $_POST["cityName"],
            $_POST["postalCode"],
            $_POST['country'],
            $_POST['garageNumber'],
            $_POST['lotNumber'],
            $_POST['rentWithoutCharges'],
            $_POST['charges'],
            $_POST['surface'],
            $_POST['reference'],
            $_POST['attachmentName'],
            $_POST['trustee'],
            $_POST['caution'],
            "",
            $_POST['ownerNote'],
            $_POST["additionalIbanId"]
        );
        $message = $success ? "Garage dupliqué avec succès." : "Erreur lors de la création du garage.";
    }
    echo $message;
}
$garageInfo = $garage->read($_SESSION["ownerId"], $_GET["id"]);
$additionalIban = new additionalibans();
$liste = $additionalIban->read($_SESSION["ownerId"]);

?>
<h1>Dupliquer le garage</h1>
<form action="" method="post">
    <div>
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : htmlspecialchars($garageInfo['address']); ?>">
    </div>

    <div>
        <label for="additionalAddress">Complément d'adresse :</label>
        <input type="text" name="additionalAddress" id="additionalAddress" value="<?php echo isset($_POST['additionalAddress']) ? htmlspecialchars($_POST['additionalAddress']) : htmlspecialchars($garageInfo['additionalAddress']); ?>">
    </div>
    <div>
        <label for="cityName">Ville :</label>
        <input type="text" name="cityName" id="cityName" value="<?php echo isset($_POST['cityName']) ? htmlspecialchars($_POST['cityName']) : htmlspecialchars($garageInfo["cityName"]); ?>">
    </div>
    <div>
        <label for="postalCode">Code postale :</label>
        <input type="text" name="postalCode" id="postalCode" value="<?php echo isset($_POST['postalCode']) ? htmlspecialchars($_POST['postalCode']) : htmlspecialchars($garageInfo["postalCode"]); ?>">
    </div>

    <div>
        <label for="country">Pays :</label>
        <input type="text" name="country" id="country" required value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : htmlspecialchars($garageInfo['country']); ?>">
    </div>

    <div>
        <label for="garageNumber">Numéro de garage :</label>
        <input type="number" name="garageNumber" id="garageNumber" required value="<?php echo isset($_POST['garageNumber']) ? htmlspecialchars($_POST['garageNumber']) :""; ?>">
    </div>

    <div>
        <label for="lotNumber">Numéro de lot :</label>
        <input type="number" name="lotNumber" id="lotNumber"  value="<?php echo isset($_POST['lotNumber']) ? htmlspecialchars($_POST['lotNumber']) : ""; ?>">
    </div>

    <div>
        <label for="rentWithoutCharges">Loyer hors charges (€) :</label>
        <input type="number" step="0.01" name="rentWithoutCharges" id="rentWithoutCharges" required value="<?php echo isset($_POST['rentWithoutCharges']) ? htmlspecialchars($_POST['rentWithoutCharges']) : htmlspecialchars($garageInfo['rentWithoutCharges']); ?>">
    </div>

    <div>
        <label for="charges">Charges (€) :</label>
        <input type="number" step="0.01" name="charges" id="charges" required value="<?php echo isset($_POST['charges']) ? htmlspecialchars($_POST['charges']) : htmlspecialchars($garageInfo['charges']); ?>">
    </div>

    <div>
        <label for="surface">Surface (m²) :</label>
        <input type="number" name="surface" id="surface" required value="<?php echo isset($_POST['surface']) ? htmlspecialchars($_POST['surface']) : htmlspecialchars($garageInfo['surface']); ?>">
    </div>

    <div>
        <label for="reference">Référence :</label>
        <input type="text" name="reference" id="reference" value="<?php echo isset($_POST['reference']) ? htmlspecialchars($_POST['reference']) : htmlspecialchars($garageInfo['reference']); ?>">
    </div>

    <div>
        <label for="attachmentName">Pièce jointe :</label>
        <input type="text" name="attachmentName" id="attachmentName" value="<?php echo isset($_POST['attachmentName']) ? htmlspecialchars($_POST['attachmentName']) : htmlspecialchars($garageInfo['attachmentName']); ?>">
    </div>

    <div>
        <label for="trustee">Syndic :</label>
        <input type="text" name="trustee" id="trustee" value="<?php echo isset($_POST['trustee']) ? htmlspecialchars($_POST['trustee']) : htmlspecialchars($garageInfo['trustee']); ?>">
    </div>

    <div>
        <label for="caution">Caution (€) :</label>
        <input type="number" step="0.01" name="caution" id="caution" value="<?php echo isset($_POST['caution']) ? htmlspecialchars($_POST['caution']) : htmlspecialchars($garageInfo['caution']); ?>">
    </div>
    <div>
        <label for="additionalIbanId">IBAN à utiliser pour ce garage:</label>
        <select name="additionalIbanId" id="additionalIbanId">
            <option value="0"<?php echo ($garageInfo['additionalIbanId'] == 0) ? 'selected' : ''; ?>>Principal</option>
            <?php foreach ($liste as $row): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($garageInfo['additionalIbanId'] == $row['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote" rows="3"><?php echo isset($_POST['ownerNote']) ? htmlspecialchars($_POST['ownerNote']) : htmlspecialchars($garageInfo['ownerNote'] ?? ''); ?></textarea>
    </div>

    <button type="submit">Enregistrer</button>
</form>