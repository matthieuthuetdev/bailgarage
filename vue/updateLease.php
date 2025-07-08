<?php
require 'vendor/autoload.php';

use Sicaa\NumberToFrWords\NumberToFrWords;

$lease = new Leases();
$currentLease = $lease->read($_SESSION['ownerId'], $_GET['id']);

if (!$currentLease) {
    echo "Bail introuvable.";
    exit;
}

if (!empty($_POST)) {
    $message = "";
    // Validation des données
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
    } elseif (empty($_POST['reference'])) {
    $message = " La référence du virement souhaité est obligatoire.";
  } else {
        $rent = floatval($_POST['rentAmount']);
        $charges = floatval($_POST['chargesAmount']);
        $total = $rent + $charges;

        $startDate = new DateTime($_POST['startDate']);
        $daysTotal = (int)$startDate->format('t');
        $daysUsed = $daysTotal - (int)$startDate->format('j') + 1;
        $prorata = round(($total / $daysTotal) * $daysUsed, 2);

        $madeThe = (new DateTime($_POST['madeThe']))->format('Y-m-d');

        // Conversion des montants entiers en lettres
        $rentInLetter    = NumberToFrWords::output((int)$rent);
        $chargesInLetter = NumberToFrWords::output((int)$charges);
        $totalInLetter   = NumberToFrWords::output((int)$total);
        $prorataInLetter = NumberToFrWords::output((int)$prorata);
        $cautionInLetter = NumberToFrWords::output((int)(floatval($_POST['caution'] ?? 0)));

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
            $_POST['ownerNote'],
            $_POST["reference"]
        );

        $message = $success
            ? "Bail mis à jour avec succès."
            : "Erreur lors de la mise à jour du bail.";
    }
    echo "<p>$message</p>";
}

$garages = array_values((new Garages())->read($_SESSION['ownerId']));
$tenants = (new Tenants())->read($_SESSION['ownerId']);
$jsonGarages = json_encode($garages);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier le bail</title>
    <style>
        /* Espace entre chaque bloc du formulaire */
        form div {
            margin-bottom: 1em;
        }
    </style>
</head>

<body>
    <h1>Modifier le bail</h1>
    <form action="" method="post">
        <div>
            <label for="tenantId">Locataire :</label>
            <select name="tenantId" id="tenantId" required>
                <option value="">Sélectionner un locataire</option>
                <?php foreach ($tenants as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $currentLease['tenantId'] == $t['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['name'] . ' ' . $t['firstName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="garageId">Garage :</label>
            <select name="garageId" id="garageId" required>
                <option value="">Sélectionner un garage</option>
                <?php foreach ($garages as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $currentLease['garageId'] == $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['address'] . ' - N°' . $g['garageNumber']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div><label for="madeThe">Fait le :</label>
            <input type="date" name="madeThe" id="madeThe" required value="<?= htmlspecialchars($currentLease['madeThe']) ?>">
        </div>

        <div><label for="madeIn">Fait à :</label>
            <input type="text" name="madeIn" id="madeIn" value="<?= htmlspecialchars($currentLease['madeIn']) ?>">
        </div>

        <div><label for="startDate">Date de début :</label>
            <input type="date" name="startDate" id="startDate" required value="<?= htmlspecialchars($currentLease['startDate']) ?>">
        </div>

        <div><label for="duration">Durée (mois) :</label>
            <input type="number" name="duration" id="duration" value="<?= htmlspecialchars($currentLease['duration']) ?>">
        </div>

        <div><label for="rentAmount">Montant du loyer (€) :</label>
            <input type="number" step="0.01" name="rentAmount" id="rentAmount" required value="<?= htmlspecialchars($currentLease['rentAmount']) ?>">
        </div>

        <div><label for="chargesAmount">Montant des charges (€) :</label>
            <input type="number" step="0.01" name="chargesAmount" id="chargesAmount" required value="<?= htmlspecialchars($currentLease['chargesAmount']) ?>">
        </div>

        <div><label for="caution">Caution (€) :</label>
            <input type="number" step="0.01" name="caution" id="caution" value="<?= htmlspecialchars($currentLease['caution']) ?>">
        </div>

        <div><label for="numberOfKey">Nombre de clés :</label>
            <input type="number" name="numberOfKey" id="numberOfKey" required value="<?= htmlspecialchars($currentLease['numberOfKey']) ?>">
        </div>

        <div><label for="numberOfBeep">Nombre de bip :</label>
            <input type="number" name="numberOfBeep" id="numberOfBeep" value="<?= htmlspecialchars($currentLease['numberOfBeep']) ?>">
        </div>
        <div>
            <label for="reference"> Références du virement souhaité</label>
            <input type="text" name="reference" id="reference" value="<?php echo htmlspecialchars($_POST['reference'] ?? '') ?>">
        </div>

        <div><label for="ownerNote">Note propriétaire :</label>
            <textarea name="ownerNote" id="ownerNote"><?= htmlspecialchars($currentLease['ownerNote']) ?></textarea>
        </div>

        <div><button type="submit">Mettre à jour le bail</button></div>
    </form>

    <script>
        const garages = <?= $jsonGarages ?>;

        function remplirGarage() {
            const g = garages.find(x => x.id == this.value);
            document.getElementById('rentAmount').value = g?.rentWithoutCharges ?? '';
            document.getElementById('chargesAmount').value = g?.charges ?? '';
            document.getElementById('caution').value = g?.caution ?? '';
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('garageId').addEventListener('change', remplirGarage);
        });
    </script>
</body>

</html>