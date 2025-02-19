<?php
session_start();
include_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

if (!isset($_GET['bail_id'])) {
    header("Location: liste_baux.php");
    exit();
}

$bail_id = $_GET['bail_id'];

// Fetch the rent and charges amounts from the database
$query = $db->prepare("SELECT montant_loyer, montant_charges FROM baux WHERE bail_id = ?");
$query->execute([$bail_id]);
$bail = $query->fetch(PDO::FETCH_ASSOC);

if (!$bail) {
    header("Location: liste_baux.php");
    exit();
}

$montant_loyer = $bail['montant_loyer'];
$montant_charges = $bail['montant_charges'];
$total_mensuel = $montant_loyer + $montant_charges;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Terminer le Bail</title>
	<link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <h1>Terminer le Bail</h1>
    <form action="terminer_bail.php" method="post">
        <input type="hidden" name="bail_id" value="<?php echo $bail_id; ?>">
        <label for="date_fin">Date de fin :</label>
        <input type="date" id="date_fin" name="date_fin" required>
        <label for="prorata_fin">Loyer du dernier mois (au prorata) :</label>
        <input type="text" id="prorata_fin" name="prorata_fin" readonly>
        <button type="submit">Terminer le bail</button>
		<button type="button" onclick="window.location.href='liste_baux.php'">Annuler</button>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dateFinInput = document.getElementById('date_fin');
            const prorataInput = document.getElementById('prorata_fin');
            const montantLoyer = <?php echo $montant_loyer; ?>; // Montant réel du loyer
            const montantCharges = <?php echo $montant_charges; ?>; // Montant réel des charges
            const totalMensuel = <?php echo $total_mensuel; ?>; // Total mensuel réel

            dateFinInput.addEventListener('change', function() {
                const dateFin = new Date(dateFinInput.value);
                const daysInMonth = new Date(dateFin.getFullYear(), dateFin.getMonth() + 1, 0).getDate();
                const prorata = totalMensuel * (dateFin.getDate() / daysInMonth);
                prorataInput.value = prorata.toFixed(2);
            });
        });
    </script>
</body>
</html>
