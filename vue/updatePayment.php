<?php
// Vérification de l'ID du paiement
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID du paiement invalide.";
    exit;
}

// Récupération du paiement existant
$payment = new Payments();
$currentPayment = $payment->read($_SESSION['ownerId'], $_GET['id']);

// Vérification si le paiement existe
if (!$currentPayment) {
    echo "Paiement introuvable.";
    exit;
}

// Traitement du formulaire soumis
if (!empty($_POST)) {
    $message = "";

    // Vérifications des données
    if (empty($_POST['leaseId']) || !is_numeric($_POST['leaseId'])) {
        $message = "Le bail est obligatoire.";
    } elseif ($_POST['status'] != 0 && $_POST['status'] != "1" ) {
        $message = "Le statut est obligatoire.";
    } elseif (empty($_POST['monthPayment'])) {
        $message = "La date du mois de paiement est obligatoire.";
    } elseif (empty($_POST['methodPayment'])) {
        $message = "La méthode de paiement est obligatoire.";
    } else {
        // Préparation des données
        $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) ? floatval($_POST['amount']) : 0.0;
        
        // Mise à jour du paiement
        $success = $payment->update(
            $_GET['id'],
            $_POST['leaseId'],
            $_POST['monthPayment'],
            $_POST['status'],
            $amount,
            $_POST['methodPayment'],
            $_POST['ownerNote'] ?? ""
        );
        
        $message = $success ? "Paiement mis à jour avec succès." : "Erreur lors de la mise à jour du paiement.";
    }

    echo "<p>$message</p>";
}

// Récupération des baux pour la liste déroulante
$lease = new Leases();
$leaseInfo = $lease->read($_SESSION['ownerId']);
?>

<h1>Modifier un paiement</h1>
<form action="" method="post">
    <div>
        <label for="leaseId">Bail :</label>
        <select name="leaseId" id="leaseId" required>
            <option value="">Sélectionner un bail</option>
            <?php foreach ($leaseInfo as $l): ?>
                <option value="<?= $l['id'] ?>" <?= ($currentPayment['leaseId'] == $l['id']) ? 'selected' : '' ?>>
                    <?php
                    $tenant = new Tenants();
                    $tenantInfo = $tenant->read($_SESSION['ownerId'], $l['tenantId']);
                    $garage = new Garages();
                    $garageInfo = $garage->read($_SESSION['ownerId'], $l['garageId']);
                    echo htmlspecialchars($tenantInfo['name'] . ' ' . $tenantInfo['firstName']) . ' - N°' . htmlspecialchars($garageInfo['garageNumber']);
                    ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="monthPayment">Date du mois concerné :</label>
        <input type="date" name="monthPayment" id="monthPayment" required value="<?= htmlspecialchars($currentPayment['monthPayment']) ?>">
    </div>

    <div>
        <label for="amount">Montant payé (€) :</label>
        <input type="number" step="0.01" name="amount" id="amount" value="<?= htmlspecialchars($currentPayment['amount']) ?>">
    </div>

    <div>
        <label for="methodPayment">Méthode de paiement :</label>
        <select name="methodPayment" id="methodPayment" required>
            <option value="">Sélectionner une méthode</option>
            <?php
            $methods = ['Carte bancaire', 'Chèque', 'Virement', 'Espèces'];
            foreach ($methods as $method):
                $selected = ($currentPayment['methodPayment'] == $method) ? 'selected' : '';
            ?>
                <option value="<?= $method ?>" <?= $selected ?>><?= $method ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="status">Statut :</label>
        <select name="status" id="status" required>
            <option value="">Sélectionner un statut</option>
            <option value="1" <?= ($currentPayment['status'] == 1) ? 'selected' : '' ?>>Payé</option>
            <option value="0" <?= ($currentPayment['status'] == 0) ? 'selected' : '' ?>>Non payé</option>
        </select>
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote"><?= empty($currentPayment['ownerNote']) ? htmlspecialchars($currentPayment['ownerNote']) : "" ?></textarea>
    </div>

    <div>
        <button type="submit">Mettre à jour le paiement</button>
    </div>
</form>