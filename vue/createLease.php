<?php

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
        $madeTheInput = $_POST["madeThe"];
        $date = new DateTime($madeTheInput);
        $madeThe = $date->format("y-m-d");
        $startDate = new DateTime($_POST['startDate']);
        $endOfMonth = new DateTime($startDate->format('Y-m-t'));
        $daysTotal = (int)$endOfMonth->format('j');
        $daysUsed = $daysTotal - (int)$startDate->format('j') + 1;
        $prorata = round(($total / $daysTotal) * $daysUsed, 2);

        $numberToletter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
        $rentInLetter = $numberToletter->format($rent);
        $chargesInLetter = $numberToletter->format($charges);
        $totalInLetter = $numberToletter->format($total);
        $prorataInLetter = $numberToletter->format($prorata);
        $cautionInLetter = $numberToletter->format(floatval($_POST['caution'] ?? 0));

        $lease = new Leases();
        $success = $lease->create(
            $_POST['tenantId'],
            $_POST['garageId'],
            $_SESSION["ownerId"],
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
        $message = $success ? "Bail créé avec succès." : "Erreur lors de la création du bail.";
    }
    echo $message;
}

$garage = new Garages();
$garages = $garage->read($_SESSION['ownerId']);

$tenant = new Tenants();
$tenants = $tenant->read($_SESSION['ownerId']);
?>

<h1>Créer un bail</h1>
<form action="" method="post">
    <div>
        <label for="tenantId">Locataire :</label>
        <select name="tenantId" id="tenantId" required>
            <option value="">Sélectionner un locataire</option>
            <?php foreach ($tenants as $t): ?>
                <option value="<?php echo $t['id']; ?>" <?php echo (isset($_POST['tenantId']) && $_POST['tenantId'] == $t['id']) ? 'selected' : ''; ?>>
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
                <option value="<?php echo $g['id']; ?>" <?php echo (isset($_POST['garageId']) && $_POST['garageId'] == $g['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($g['address'] . ' - N°' . $g['garageNumber']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="madeThe">Fait le :</label>
        <input type="date" name="madeThe" id="madeThe" required value="<?php echo isset($_POST['madeThe']) ? htmlspecialchars($_POST['madeThe']) : date('Y-m-d'); ?>">
    </div>

    <div>
        <label for="madeIn">Fait à :</label>
        <input type="text" name="madeIn" id="madeIn" value="<?php echo isset($_POST['madeIn']) ? htmlspecialchars($_POST['madeIn']) : ''; ?>">
    </div>

    <div>
        <label for="startDate">Date de début :</label>
        <input type="date" name="startDate" id="startDate" required value="<?php echo isset($_POST['startDate']) ? htmlspecialchars($_POST['startDate']) :  date('Y-m-d'); ?>">
    </div>

    <div>
        <label for="duration">Durée (mois) :</label>
        <input type="number" name="duration" id="duration" value="<?php echo isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : ''; ?>">
    </div>

    <div>
        <label for="rentAmount">Montant du loyer (€) :</label>
        <input type="number" step="0.01" name="rentAmount" id="rentAmount" required value="<?php echo isset($_POST['rentAmount']) ? htmlspecialchars($_POST['rentAmount']) : ''; ?>">
    </div>

    <div>
        <label for="chargesAmount">Montant des charges (€) :</label>
        <input type="number" step="0.01" name="chargesAmount" id="chargesAmount" required value="<?php echo isset($_POST['chargesAmount']) ? htmlspecialchars($_POST['chargesAmount']) : ''; ?>">
    </div>

    <div>
        <label for="caution">Caution (€) :</label>
        <input type="number" step="0.01" name="caution" id="caution" value="<?php echo isset($_POST['caution']) ? htmlspecialchars($_POST['caution']) : ''; ?>">
    </div>

    <div>
        <label for="numberOfKey">Nombre de clés :</label>
        <input type="number" name="numberOfKey" id="numberOfKey" required value="<?php echo isset($_POST['numberOfKey']) ? htmlspecialchars($_POST['numberOfKey']) : ''; ?>">
    </div>

    <div>
        <label for="numberOfBeep">Nombre de bip :</label>
        <input type="number" name="numberOfBeep" id="numberOfBeep" value="<?php echo isset($_POST['numberOfBeep']) ? htmlspecialchars($_POST['numberOfBeep']) : ''; ?>">
    </div>

    <div>
        <label for="attachmentId">Pièces jointes :</label>
        <input type="text" name="attachmentId" id="attachmentId" value="<?php echo isset($_POST['attachmentId']) ? htmlspecialchars($_POST['attachmentId']) : ''; ?>">
    </div>

    <div>
        <label for="ownerNote">Note propriétaire :</label>
        <textarea name="ownerNote" id="ownerNote"><?php echo isset($_POST['ownerNote']) ? htmlspecialchars($_POST['ownerNote']) : ''; ?></textarea>
    </div>

    <div>
        <button type="submit">Créer le bail</button>
    </div>
</form>