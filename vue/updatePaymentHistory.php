<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID du paiement invalide.";
    exit;
}

$paymentHistory = new PaymentHistories();
$currentPaymentHistory = $paymentHistory->read($_GET['id']);

if (!$currentPaymentHistory) {
    echo "Historique de paiement introuvable.";
    exit;
}

if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['leaseId']) || !is_numeric($_POST['leaseId'])) {
        $message = "Le bail est obligatoire.";
    } elseif (empty($_POST['status'])) {
        $message = "Le statut est obligatoire.";
    } elseif (empty($_POST['monthPayment'])) {
        $message = "La date précise du paiement est obligatoire.";
    } else {
        $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) ? floatval($_POST['amount']) : 0.0;

        $success = $paymentHistory->update(
            $_GET['id'],
            $_POST['leaseId'],
            $amount,
            $_POST['monthPayment']
        );

        $message = $success ? "Historique de paiement mis à jour avec succès." : "Erreur lors de la mise à jour de l'historique de paiement.";
    }

    echo "<p>$message</p>";
}

$lease = new Leases();
$leases = $lease->read($_SESSION['ownerId']);
?>

<h1>Modifier un paiement</h1>
<form action="" method="post">
    <div>
        <label for="leaseId">Bail :</label>
        <select name="leaseId" id="leaseId" required>
            <option value="">Sélectionner un bail</option>
            <?php foreach ($leases as $l): ?>
                <option value="<?= $l['id'] ?>" <?= ($currentPaymentHistory['leaseId'] == $l['id']) ? 'selected' : '' ?>>
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
        <label for="monthPayment">Date précise du paiement :</label>
        <input type="date" name="monthPayment" id="monthPayment" required value="<?= htmlspecialchars($currentPaymentHistory['monthPayment']) ?>">
    </div>

    <div>
        <label for="amount">Montant payé (€) :</label>
        <input type="number" step="0.01" name="amount" id="amount" value="<?= htmlspecialchars($currentPaymentHistory['amount']) ?>">
    </div>

    <div>
        <label for="status">Statut :</label>
        <input type="text" name="status" id="status" required placeholder="Indiquez 'Payé' ou 'Non payé'" value="<?= htmlspecialchars($currentPaymentHistory['status']) ?>">
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote"><?= htmlspecialchars($currentPaymentHistory['ownerNote']) ?></textarea>
    </div>

    <div>
        <button type="submit">Mettre à jour l'historique de paiement</button>
    </div>
</form>X