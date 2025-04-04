<?php
if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['address'])) {
        $message = "L'adresse est obligatoire.";
    } elseif (empty($_POST['country'])) {
        $message = "Le pays est obligatoire.";
    } elseif (empty($_POST['garageNumber']) || !is_numeric($_POST['garageNumber'])) {
        $message = "Le numéro de garage est obligatoire.";
    } elseif (empty($_POST['lotNumber']) || !is_numeric($_POST['lotNumber'])) {
        $message = "Le numéro de lot est obligatoire.";
    } elseif (empty($_POST['rentWithoutCharges']) || !is_numeric($_POST['rentWithoutCharges'])) {
        $message = "Le loyer hors charges est obligatoire.";
    } elseif (empty($_POST['charges']) || !is_numeric($_POST['charges'])) {
        $message = "Les charges sont obligatoires.";
    } elseif (empty($_POST['surface']) || !is_numeric($_POST['surface'])) {
        $message = "La surface est obligatoire.";
    } else {
        $garage = new Garages();
        $success = $garage->create(
            $_SESSION['ownerId'],
            $_POST['address'],
            $_POST['additionalAddress'],
            1,
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
        $message = $success ? "Garage créé avec succès." : "Erreur lors de la création du garage.";
    }
    echo "$message";
}
$additionalIban = new additionalibans();
$liste = $additionalIban->read($_SESSION["ownerId"]);
?>
<h1>Créer un garage</h1>
<form action="" method="post">
    <div>
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
    </div>

    <div>
        <label for="additionalAddress">Complément d'adresse :</label>
        <input type="text" name="additionalAddress" id="additionalAddress" value="<?php echo isset($_POST['additionalAddress']) ? htmlspecialchars($_POST['additionalAddress']) : ''; ?>">
    </div>

    <div>
        <label for="country">Pays :</label>
        <input type="text" name="country" id="country" required value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : 'France'; ?>">
    </div>

    <div>
        <label for="garageNumber">Numéro de garage :</label>
        <input type="number" name="garageNumber" id="garageNumber" required value="<?php echo isset($_POST['garageNumber']) ? htmlspecialchars($_POST['garageNumber']) : ''; ?>">
    </div>

    <div>
        <label for="lotNumber">Numéro de lot :</label>
        <input type="number" name="lotNumber" id="lotNumber" required value="<?php echo isset($_POST['lotNumber']) ? htmlspecialchars($_POST['lotNumber']) : ''; ?>">
    </div>

    <div>
        <label for="rentWithoutCharges">Loyer hors charges (€) :</label>
        <input type="number" step="0.01" name="rentWithoutCharges" id="rentWithoutCharges" required value="<?php echo isset($_POST['rentWithoutCharges']) ? htmlspecialchars($_POST['rentWithoutCharges']) : ''; ?>">
    </div>

    <div>
        <label for="charges">Charges (€) :</label>
        <input type="number" step="0.01" name="charges" id="charges" required value="<?php echo isset($_POST['charges']) ? htmlspecialchars($_POST['charges']) : ''; ?>">
    </div>

    <div>
        <label for="surface">Surface (m²) :</label>
        <input type="number" name="surface" id="surface" required value="<?php echo isset($_POST['surface']) ? htmlspecialchars($_POST['surface']) : ''; ?>">
    </div>

    <div>
        <label for="reference">Référence :</label>
        <input type="text" name="reference" id="reference" value="<?php echo isset($_POST['reference']) ? htmlspecialchars($_POST['reference']) : ''; ?>">
    </div>

    <div>
        <label for="attachmentName">Pièce jointe :</label>
        <input type="text" name="attachmentName" id="attachmentName" value="<?php echo isset($_POST['attachmentName']) ? htmlspecialchars($_POST['attachmentName']) : ''; ?>">
    </div>

    <div>
        <label for="trustee">Syndic :</label>
        <input type="text" name="trustee" id="trustee" value="<?php echo isset($_POST['trustee']) ? htmlspecialchars($_POST['trustee']) : ''; ?>">
    </div>

    <div>
        <label for="caution">Caution (€) :</label>
        <input type="number" step="0.01" name="caution" id="caution" value="<?php echo isset($_POST['caution']) ? htmlspecialchars($_POST['caution']) : ''; ?>">
    </div>
    <div>
        <label for="additionalIbanId">IBAN à utiliser pour ce garage:</label>
        <select name="additionalIbanId" id="additionalIbanId" required>
            <option value="0">Par défaut</option>
            <?php foreach ($liste as $row): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote" rows="3"><?php echo isset($_POST['ownerNote']) ? htmlspecialchars($_POST['ownerNote']) : ''; ?></textarea>
    </div>


    <button type="submit">Enregistrer</button>
</form>