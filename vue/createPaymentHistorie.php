<?php

if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['leaseId']) || !is_numeric($_POST['leaseId'])) {
        $message = "Le bail est obligatoire.";
    } elseif (empty($_POST['monthPayment'])) {
        $message = "La date précise du paiement est obligatoire.";
    } elseif (empty($_POST['status'])) {
        $message = "Le statut est obligatoire.";
    } else {
        $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) ? floatval($_POST['amount']) : 0.0;

        $paymentHistory = new PaymentHistories();
        $success = $paymentHistory->create(
            $_POST['leaseId'],
            $_POST['status'],  // Statut du paiement
            $amount,
            $_POST['monthPayment']  // Date précise du paiement
        );
        $message = $success ? "Historique de paiement créé avec succès." : "Erreur lors de la création de l'historique de paiement.";
    }
    echo $message;
}

$lease = new Leases();
$leases = $lease->read($_SESSION['ownerId']);
?>

<h1>Ajouter un paiement à l'historique</h1>
<form action="" method="post">
    <div>
        <label for="leaseId">Bail :</label>
        <select name="leaseId" id="leaseId" required>
            <option value="">Sélectionner un bail</option>
            <?php foreach ($leases as $l): ?>
                <option value="<?php echo $l['id']; ?>" <?php echo (isset($_POST['leaseId']) && $_POST['leaseId'] == $l['id']) ? 'selected' : ''; ?>>
                    <?php
                    // Chargement du locataire et garage liés au bail
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
        <input type="date" name="monthPayment" id="monthPayment" required value="<?php echo isset($_POST['monthPayment']) ? htmlspecialchars($_POST['monthPayment']) : ''; ?>">
    </div>

    <div>
        <label for="amount">Montant payé (€) :</label>
        <input type="number" step="0.01" name="amount" id="amount" value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
    </div>

    <div>
        <label for="status">Statut :</label>
        <input type="text" name="status" id="status" required placeholder="Indiquez 'Payé' ou 'Non payé'" value="<?php echo isset($_POST['status']) ? htmlspecialchars($_POST['status']) : ''; ?>">
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote"><?php echo isset($_POST['ownerNote']) ? htmlspecialchars($_POST['ownerNote']) : ''; ?></textarea>
    </div>

    <div>
        <button type="submit">Créer l'historique de paiement</button>
    </div>
</form>