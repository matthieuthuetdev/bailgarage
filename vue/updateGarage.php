<link rel="stylesheet" href="./css/forms.css">
<main class="main-content">
    <h1>Modifier le garage</h1>
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
            $success = $garage->update(
                $_GET["id"],
                $_SESSION['ownerId'],
                $_POST['address'],
                $_POST['additionalAddress'],
                1,
                $_POST["cityName"],
                $_POST["postalCode"],
                $_POST['country'],
                $_POST['garageNumber'],
                $_POST['lotNumber'],
                $_POST['rentWithoutCharges'],
                $_POST['charges'],
                $_POST['surface'],
                $_POST['reference'],
                $_POST['trustee'],
                $_POST['caution'],
                $_POST["additionalIbanId"],
                "",
                $_POST['ownerNote']
            );
            $message = $success ? "Garage modifié avec succès." : "Erreur lors de la création du garage.";
        }
        echo "<p>{$message}</p>";
    }

    $garageInfo = $garage->read($_SESSION["ownerId"], $_GET["id"]);
    $additionalIban = new additionalibans();
    $liste = $additionalIban->read($_SESSION["ownerId"]);
    ?>
    
    <form class="form-card" action="" method="post">
        <div class="form-sections">
            <div class="form-left">
                <div>
                    <label for="address">Adresse :</label>
                    <input type="text" name="address" id="address" required
                        value="<?= htmlspecialchars($_POST['address'] ?? $garageInfo['address']) ?>" />
                </div>
                <div>
                    <label for="additionalAddress">Complément d'adresse :</label>
                    <input type="text" name="additionalAddress" id="additionalAddress"
                        value="<?= htmlspecialchars($_POST['additionalAddress'] ?? $garageInfo['additionalAddress']) ?>" />
                </div>
                <div>
                    <label for="cityName">Ville :</label>
                    <input type="text" name="cityName" id="cityName"
                        value="<?= htmlspecialchars($_POST['cityName'] ?? $garageInfo["cityName"]) ?>" />
                </div>
                <div>
                    <label for="postalCode">Code postal :</label>
                    <input type="text" name="postalCode" id="postalCode"
                        value="<?= htmlspecialchars($_POST['postalCode'] ?? $garageInfo["postalCode"]) ?>" />
                </div>
                <div>
                    <label for="country">Pays :</label>
                    <input type="text" name="country" id="country" required
                        value="<?= htmlspecialchars($_POST['country'] ?? $garageInfo['country']) ?>" />
                </div>
                <div>
                    <label for="garageNumber">Numéro de garage :</label>
                    <input type="number" name="garageNumber" id="garageNumber" required
                        value="<?= htmlspecialchars($_POST['garageNumber'] ?? $garageInfo['garageNumber']) ?>" />
                </div>
                <div>
                    <label for="lotNumber">Numéro de lot :</label>
                    <input type="number" name="lotNumber" id="lotNumber"
                        value="<?= htmlspecialchars($_POST['lotNumber'] ?? $garageInfo['lotNumber']) ?>" />
                </div>
                <div>
                    <label for="rentWithoutCharges">Loyer hors charges (€) :</label>
                    <input type="number" step="0.01" name="rentWithoutCharges" id="rentWithoutCharges" required
                        value="<?= htmlspecialchars($_POST['rentWithoutCharges'] ?? $garageInfo['rentWithoutCharges']) ?>" />
                </div>
                <div>
                    <label for="charges">Charges (€) :</label>
                    <input type="number" step="0.01" name="charges" id="charges" required
                        value="<?= htmlspecialchars($_POST['charges'] ?? $garageInfo['charges']) ?>" />
                </div>
            </div>

            <div class="form-right">
                <div>
                    <label for="surface">Surface (m²) :</label>
                    <input type="number" name="surface" id="surface" required
                        value="<?= htmlspecialchars($_POST['surface'] ?? $garageInfo['surface']) ?>" />
                </div>
                <div>
                    <label for="reference">Référence :</label>
                    <input type="text" name="reference" id="reference"
                        value="<?= htmlspecialchars($_POST['reference'] ?? $garageInfo['reference']) ?>" />
                </div>
                <div>
                    <label for="trustee">Syndic :</label>
                    <input type="text" name="trustee" id="trustee"
                        value="<?= htmlspecialchars($_POST['trustee'] ?? $garageInfo['trustee']) ?>" />
                </div>
                <div>
                    <label for="caution">Caution (€) :</label>
                    <input type="number" step="0.01" name="caution" id="caution"
                        value="<?= htmlspecialchars($_POST['caution'] ?? $garageInfo['caution']) ?>" />
                </div>
                <div>
                    <label for="additionalIbanId">IBAN à utiliser pour ce garage :</label>
                    <select name="additionalIbanId" id="additionalIbanId">
                        <option value="0" <?= $garageInfo['additionalIbanId'] == 0 ? 'selected' : ''; ?>>Principal</option>
                        <?php foreach ($liste as $row): ?>
                            <option value="<?= $row['id'] ?>" <?= $garageInfo['additionalIbanId'] == $row['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($row['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="ownerNote">Note du propriétaire :</label>
                    <textarea name="ownerNote" id="ownerNote" rows="3"><?= htmlspecialchars($_POST['ownerNote'] ?? $garageInfo['ownerNote'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <div class="form-footer">
            <button type="submit">Enregistrer</button>
        </div>
    </form>
</main>
