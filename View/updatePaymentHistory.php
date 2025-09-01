<?php
$paymentHistory = new PaymentHistories();
$currentPayment = $paymentHistory->read($_SESSION["ownerId"], $_GET["id"]);

if ($currentPayment) {
    if (!empty($_POST)) {
        $message = "";

        if (empty($_POST['leaseId']) || !is_numeric($_POST['leaseId'])) {
            $message = "Le bail est obligatoire.";
        } elseif (empty($_POST['monthPayment'])) {
            $message = "La date du paiement est obligatoire.";
        } else {
            $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) ? floatval($_POST['amount']) : 0.0;

            $success = $paymentHistory->update(
                $_GET["id"],
                $_POST['leaseId'],
                $amount,
                $_POST['monthPayment'],
                $_POST['methodPayment'] // Here we get the payment method
            );
            $message = $success ? "Paiement mis à jour avec succès." : "Erreur lors de la mise à jour de l'historique de paiement.";
        }
        echo $message;
    }

    $lease = new Leases();
    $leases = $lease->read($_SESSION['ownerId']);
} else {
    echo "Historique de paiement introuvable.";
    exit;
}
?>

<h1>Mettre à jour le paiement à l'historique</h1>
<form action="" method="post">
    <div>
        <label for="leaseId">Bail :</label>
        <select name="leaseId" id="leaseId" required>
            <option value="">Sélectionner un bail</option>
            <?php foreach ($leases as $l): ?>
                <option value="<?php echo $l['id']; ?>" <?php echo ($currentPayment['leasesId'] == $l['id']) ? 'selected' : ''; ?>>
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
        <label for="monthPayment">Date du paiement :</label>
        <input type="date" name="monthPayment" id="monthPayment" required value="<?php echo $currentPayment["paymentDate"]; ?>">
    </div>

    <div>
        <label for="amount">Montant payé (€) :</label>
        <input type="number" step="0.01" name="amount" id="amount" value="<?php echo htmlspecialchars($currentPayment['amount']); ?>">
    </div>

    <div>
        <label for="methodPayment">Méthode de paiement :</label>
        <select name="methodPayment" id="methodPayment" required>
            <option value="">Sélectionner une méthode</option>
            <option value="Carte bancaire" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Carte bancaire' ? 'selected' : ($currentPayment['methode'] == 'Carte bancaire' ? 'selected' : ''); ?>>Carte bancaire</option>
            <option value="Chèque" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Chèque' ? 'selected' : ($currentPayment['methode'] == 'Chèque' ? 'selected' : ''); ?>>Chèque</option>
            <option value="Virement" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Virement' ? 'selected' : ($currentPayment['methode'] == 'Virement' ? 'selected' : ''); ?>>Virement</option>
            <option value="Espèces" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Espèces' ? 'selected' : ($currentPayment['methode'] == 'Espèces' ? 'selected' : ''); ?>>Espèces</option>
        </select>
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote"><?php echo isset($_POST['ownerNote']) ? htmlspecialchars($_POST['ownerNote']) : ''; ?></textarea>
    </div>

    <div>
        <button type="submit">Mettre à jour l'historique de paiement</button>
    </div>
</form>
