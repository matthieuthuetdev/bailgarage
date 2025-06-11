<?php
require 'vendor/autoload.php'; // Composer autoload
use Sicaa\NumberToFrWords\NumberToFrWords;

if (!empty($_POST)) {
    $message = "";
    if (!isset($_POST['tenantId']) || trim($_POST['tenantId']) === '' || !filter_var($_POST['tenantId'], FILTER_VALIDATE_INT) || (int)$_POST['tenantId'] === 0) {
        $message = "Le locataire est obligatoire.";
    } elseif (!isset($_POST['garageId']) || trim($_POST['garageId']) === '' || !filter_var($_POST['garageId'], FILTER_VALIDATE_INT) || (int)$_POST['garageId'] === 0) {
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

        $start = new DateTime($_POST['startDate']);
        $daysTotal = (int)$start->format('t');
        $daysUsed = $daysTotal - (int)$start->format('j') + 1;
        $prorata = round(($total / $daysTotal) * $daysUsed, 2);

        // Conversion de la partie entière en lettres
        $rentInLetter = NumberToFrWords::output((int)$rent);
        $chargesInLetter = NumberToFrWords::output((int)$charges);
        $totalInLetter = NumberToFrWords::output((int)$total);
        $prorataInLetter = NumberToFrWords::output((int)$prorata);
        $cautionInLetter = NumberToFrWords::output((int)(floatval($_POST['caution'] ?? 0)));

        $madeThe = (new DateTime($_POST['madeThe']))->format('y-m-d');

        $lease = new Leases();
        $success = $lease->create(
            $_POST['tenantId'], $_POST['garageId'], $_SESSION['ownerId'], $madeThe,
            $_POST['madeIn'], $_POST['startDate'], $_POST['duration'], $rent,
            $rentInLetter, $charges, $chargesInLetter, $total, $totalInLetter,
            $prorata, $prorataInLetter, $_POST['caution'], $cautionInLetter,
            $_POST['numberOfKey'], $_POST['numberOfBeep'], 1,
            $_POST['attachmentPath'], $_POST['ownerNote']
        );
        $message = $success ? "Bail créé avec succès." : "Erreur lors de la création du bail.";
    }

    echo "<p>{$message}</p>";
}

$garages = array_values((new Garages())->read($_SESSION['ownerId']));
$tenants = (new Tenants())->read($_SESSION['ownerId']);
$jsonGarages = json_encode($garages);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Créer un bail</title>
</head>
<body>
  <h1>Créer un bail</h1>

  <form action="" method="post">
    <div>
      <label for="tenantId">Locataire :</label>
      <select name="tenantId" id="tenantId" required>
        <option value="">Sélectionner un locataire</option>
        <?php foreach ($tenants as $t): ?>
          <option value="<?= $t['id'] ?>" <?= (isset($_POST['tenantId']) && $_POST['tenantId'] == $t['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($t['name'] . ' ' . $t['firstName']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="garageId">Garage :</label>
      <select name="garageId" id="garageId" required>
        <option value="">Sélectionner un garage</option>
        <?php foreach ($garages as $g): ?>
          <option value="<?= $g['id'] ?>" <?= (isset($_POST['garageId']) && $_POST['garageId'] == $g['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($g['address'] . ' - N°' . $g['garageNumber']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="madeThe">Fait le :</label>
      <input type="date" name="madeThe" id="madeThe" required value="<?= htmlspecialchars($_POST['madeThe'] ?? date('Y-m-d')) ?>">
    </div>

    <div>
      <label for="madeIn">Fait à :</label>
      <input type="text" name="madeIn" id="madeIn" value="<?= htmlspecialchars($_POST['madeIn'] ?? '') ?>">
    </div>

    <div>
      <label for="startDate">Date de début :</label>
      <input type="date" name="startDate" id="startDate" required value="<?= htmlspecialchars($_POST['startDate'] ?? date('Y-m-d')) ?>">
    </div>

    <div>
      <label for="duration">Durée (mois) :</label>
      <input type="number" name="duration" id="duration" value="<?= htmlspecialchars($_POST['duration'] ?? '') ?>">
    </div>

    <div>
      <label for="rentAmount">Montant du loyer (€) :</label>
      <input type="number" step="0.01" name="rentAmount" id="rentAmount" required value="<?= htmlspecialchars($_POST['rentAmount'] ?? '') ?>">
    </div>

    <div>
      <label for="chargesAmount">Montant des charges (€) :</label>
      <input type="number" step="0.01" name="chargesAmount" id="chargesAmount" required value="<?= htmlspecialchars($_POST['chargesAmount'] ?? '') ?>">
    </div>

    <div>
      <label for="caution">Caution (€) :</label>
      <input type="number" step="0.01" name="caution" id="caution" value="<?= htmlspecialchars($_POST['caution'] ?? '') ?>">
    </div>

    <div>
      <label for="numberOfKey">Nombre de clés :</label>
      <input type="number" name="numberOfKey" id="numberOfKey" required value="<?= htmlspecialchars($_POST['numberOfKey'] ?? '') ?>">
    </div>

    <div>
      <label for="numberOfBeep">Nombre de bip :</label>
      <input type="number" name="numberOfBeep" id="numberOfBeep" value="<?= htmlspecialchars($_POST['numberOfBeep'] ?? '') ?>">
    </div>

    <div>
      <label for="attachmentPath">Pièces jointes :</label>
      <input type="text" name="attachmentPath" id="attachmentPath" value="<?= htmlspecialchars($_POST['attachmentPath'] ?? '') ?>">
    </div>

    <div>
      <label for="ownerNote">Note propriétaire :</label>
      <textarea name="ownerNote" id="ownerNote"><?= htmlspecialchars($_POST['ownerNote'] ?? '') ?></textarea>
    </div>

    <div>
      <button type="submit">Créer le bail</button>
    </div>
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
