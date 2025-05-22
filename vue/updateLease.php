<?php
$lease = new Leases();
$currentLease = $lease->read($_SESSION['ownerId'], $_GET['id']);

if (!$currentLease) {
    echo "Bail introuvable.";
    exit;
}

if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['tenantId']) || !is_numeric($_POST['tenantId'])) {
        $message = "Le locataire est obligatoire.";
    } elseif (empty($_POST['garageId']) || !is_numeric($_POST['garageId'])) {
        $message = "Le garage est obligatoire.";
    } elseif (empty($_POST['madeThe'])) {
        $message = "La date de création est obligatoire.";
    } elseif (empty($_POST['startDate'])) {
        $message = "La date de début est obligatoire.";
    } elseif (empty($_POST['rentAmount']) || !is_numeric($_POST['rentAmount'])) {
        $message = "Le montant du loyer est obligatoire.";
    } elseif (empty($_POST['chargesAmount']) || !is_numeric($_POST['chargesAmount'])) {
        $message = "Le montant des charges est obligatoire.";
    } elseif (empty($_POST['numberOfKey']) || !is_numeric($_POST['numberOfKey'])) {
        $message = "Le nombre de clés est obligatoire.";
    } else {
        $rent = floatval($_POST['rentAmount']);
        $charges = floatval($_POST['chargesAmount']);
        $total = $rent + $charges;

        $startDate = new DateTime($_POST['startDate']);
        $endOfMonth = new DateTime($startDate->format('Y-m-t'));
        $daysTotal = (int)$endOfMonth->format('j');
        $daysUsed = $daysTotal - (int)$startDate->format('j') + 1;
        $prorata = round(($total / $daysTotal) * $daysUsed, 2);
        $madeTheInput = $_POST["madeThe"];
        $date = new DateTime($madeTheInput);
        $madeThe = $date->format("Y-m-d");
        $numberToletter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
        $rentInLetter = $numberToletter->format($rent);
        $chargesInLetter = $numberToletter->format($charges);
        $totalInLetter = $numberToletter->format($total);
        $prorataInLetter = $numberToletter->format($prorata);
        $cautionInLetter = $numberToletter->format(floatval($_POST['caution'] ?? 0));
        $success = $lease->update(
            $_GET['id'],
            $_POST['tenantId'],
            $_POST['garageId'],
            $madeThe,
            $_POST['madeIn'],
            $_POST['startDate'],
            $_POST['duration'],
            $rent,
            $rentInLetter,
            $charges,
            $chargesInLetter,
            $total,
            $totalInLetter,
            $prorata,
            $prorataInLetter,
            $_POST['caution'],
            $cautionInLetter,
            $_POST['numberOfKey'],
            $_POST['numberOfBeep'],
            1,
            $_POST['attachmentId'],
            $_POST['ownerNote']
        );
        $message = $success ? "Bail mis à jour avec succès." : "Erreur lors de la mise à jour du bail.";
    }
    echo $message;
}

// Récupération des garages et locataires
$garage = new Garages();
$garages = $garage->read($_SESSION['ownerId']);

$tenant = new Tenants();
$tenants = $tenant->read($_SESSION['ownerId']);
?>

<h1>Modifier le bail</h1>
<form action="" method="post">
    <div>
        <label for="tenantId">Locataire :</label>
        <select name="tenantId" id="tenantId" required>
            <option value="">Sélectionner un locataire</option>
            <?php foreach ($tenants as $t): ?>
                <option value="<?php echo $t['id']; ?>" <?php echo ($currentLease['tenantId'] == $t['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($t['name'] . ' ' . $t['firstName']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="garageId">Garage :</label>
        <select name="garageId" id="garageId" required>
            <option value="">Sélectionner un garage</option>
            <?php foreach ($garages as $g): ?>
                <option value="<?php echo $g['id']; ?>" <?php echo ($currentLease['garageId'] == $g['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($g['address'] . ' - N°' . $g['garageNumber']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="madeThe">Fait le :</label>
        <input type="date" name="madeThe" id="madeThe" required value="<?php echo htmlspecialchars($currentLease['madeThe']); ?>">
    </div>

    <div>
        <label for="madeIn">Fait à :</label>
        <input type="text" name="madeIn" id="madeIn" value="<?php echo htmlspecialchars($currentLease['madeIn']); ?>">
    </div>

    <div>
        <label for="startDate">Date de début :</label>
        <input type="date" name="startDate" id="startDate" required value="<?php echo htmlspecialchars($currentLease['startDate']); ?>">
    </div>

    <div>
        <label for="duration">Durée (mois) :</label>
        <input type="number" name="duration" id="duration" value="<?php echo htmlspecialchars($currentLease['duration']); ?>">
    </div>

    <div>
        <label for="rentAmount">Montant du loyer (€) :</label>
        <input type="number" step="0.01" name="rentAmount" id="rentAmount" required value="<?php echo htmlspecialchars($currentLease['rentAmount']); ?>">
    </div>

    <div>
        <label for="chargesAmount">Montant des charges (€) :</label>
        <input type="number" step="0.01" name="chargesAmount" id="chargesAmount" required value="<?php echo htmlspecialchars($currentLease['chargesAmount']); ?>">
    </div>

    <div>
        <label for="caution">Caution (€) :</label>
        <input type="number" step="0.01" name="caution" id="caution" value="<?php echo htmlspecialchars($currentLease['caution']); ?>">
    </div>

    <div>
        <label for="numberOfKey">Nombre de clés :</label>
        <input type="number" name="numberOfKey" id="numberOfKey" required value="<?php echo htmlspecialchars($currentLease['numberOfKey']); ?>">
    </div>

    <div>
        <label for="numberOfBeep">Nombre de bip :</label>
        <input type="number" name="numberOfBeep" id="numberOfBeep" value="<?php echo htmlspecialchars($currentLease['numberOfBeep']); ?>">
    </div>

    <div>
        <label for="attachmentId">Pièces jointes :</label>
        <input type="text" name="attachmentId" id="attachmentId" value="<?php echo htmlspecialchars($currentLease['attachmentId']); ?>">
    </div>

    <div>
        <label for="ownerNote">Note propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote"><?php echo htmlspecialchars($currentLease['ownerNote']); ?></textarea>
    </div>

    <div>
        <button type="submit">Mettre à jour le bail</button>
    </div>
</form>