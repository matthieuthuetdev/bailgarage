<?php

if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['leaseId']) || !is_numeric($_POST['leaseId'])) {
        $message = "Le bail est obligatoire.";
    } elseif (empty($_POST['status'])) {
        $message = "Le statut est obligatoire.";
    } elseif (empty($_POST['paymentDate'])) {
        $message = "La date de paiement est obligatoire.";
    } elseif (empty($_POST['monthPayment'])) {
        $message = "La date du mois de paiement est obligatoire.";
    } else {
        // Si le montant n'est pas défini, on le met à 0
        $amount = isset($_POST['amount']) && is_numeric($_POST['amount']) ? floatval($_POST['amount']) : 0.0;

        $payment = new Payments();
        $success = $payment->create(
            $_POST['leaseId'],
            $_POST['monthPayment'],  // Mois de paiement en format date
            $_POST['status'],
            $amount,
            $_POST['methodPayment'],
            $_POST['ownerNote']
        );
        $message = $success ? "Paiement créé avec succès." : "Erreur lors de la création du paiement.";
    }
    echo $message;
}

$lease = new Leases();
$leases = $lease->read($_SESSION['ownerId']);
?>

<h1>Créer un paiement</h1>
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
        <label for="monthPayment">Date du mois concerné :</label>
        <input type="date" name="monthPayment" id="monthPayment" required value="<?php echo isset($_POST['monthPayment']) ? htmlspecialchars($_POST['monthPayment']) : ''; ?>">
    </div>

    <div>
        <label for="amount">Montant payé (€) :</label>
        <input type="number" step="0.01" name="amount" id="amount" value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
    </div>


    <div>
        <label for="methodPayment">Méthode de paiement :</label>
        <select name="methodPayment" id="methodPayment" required>
            <option value="">Sélectionner une méthode</option>
            <option value="Carte bancaire" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Carte bancaire' ? 'selected' : ''; ?>>Carte bancaire</option>
            <option value="Chèque" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Chèque' ? 'selected' : ''; ?>>Chèque</option>
            <option value="Virement" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Virement' ? 'selected' : ''; ?>>Virement</option>
            <option value="Espèces" <?php echo isset($_POST['methodPayment']) && $_POST['methodPayment'] == 'Espèces' ? 'selected' : ''; ?>>Espèces</option>
        </select>
    </div>

    <div>
        <label for="status">Statut :</label>
        <select name="status" id="status" required>
            <option value="1" <?php echo isset($_POST['status']) && $_POST['status'] == 1 ? 'selected' : ''; ?>>Payé</option>
            <option value="0" <?php echo isset($_POST['status']) && $_POST['status'] == 0 ? 'selected' : ''; ?>>Non payé</option>
        </select>
    </div>

    <div>
        <label for="ownerNote">Note du propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote"><?php echo isset($_POST['ownerNote']) ? htmlspecialchars($_POST['ownerNote']) : ''; ?></textarea>
    </div>

    <div>
        <button type="submit">Créer le paiement</button>
    </div>
</form>
