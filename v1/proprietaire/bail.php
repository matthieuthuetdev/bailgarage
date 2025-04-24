<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once '../includes/db.php';
session_start();

// Vérification de l'authentification de l'utilisateur et de son rôle
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération de l'ID du propriétaire associé à l'utilisateur connecté
$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_id = $stmt->fetchColumn();
$stmt->closeCursor();

if (!$proprietaire_id) {
    echo "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
    exit();
}

// Récupération des locataires et des garages associés au propriétaire
$stmt_locataires = $db->prepare("SELECT l.locataire_id, l.nom, l.prenom FROM locataires l WHERE l.proprietaire_id= ? ORDER BY l.nom, l.prenom");
$stmt_locataires->bindParam(1, $proprietaire_id, PDO::PARAM_INT);
$stmt_locataires->execute();
$locataires = $stmt_locataires->fetchAll(PDO::FETCH_ASSOC);

$stmt_garages = $db->prepare("SELECT g.garage_id, g.addresse, g.numero_garage, g.loyer_hors_charge, g.charge, g.caution, g.ville FROM garages g WHERE g.proprietaire_id = ?");
$stmt_garages->bindParam(1, $proprietaire_id, PDO::PARAM_INT);
$stmt_garages->execute();
$garages = $stmt_garages->fetchAll(PDO::FETCH_ASSOC);

$message = "";

function convertNumberToWords($number) {
    $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
    return $f->format($number);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation et nettoyage des données du formulaire
    $locataire_id = filter_input(INPUT_POST, 'locataire_id', FILTER_SANITIZE_NUMBER_INT);
    $garage_id = filter_input(INPUT_POST, 'garage_id', FILTER_SANITIZE_NUMBER_INT);
    $fait_le = filter_input(INPUT_POST, 'fait_le', FILTER_SANITIZE_STRING);
    $fait_a = filter_input(INPUT_POST, 'fait_a', FILTER_SANITIZE_STRING);
    $date_debut = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
    $duree = filter_input(INPUT_POST, 'duree', FILTER_SANITIZE_NUMBER_INT);
    $caution = filter_input(INPUT_POST, 'caution', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $nombre_de_clefs = filter_input(INPUT_POST, 'nombre_de_clefs', FILTER_SANITIZE_NUMBER_INT);
    $nombre_de_bips = filter_input(INPUT_POST, 'nombre_de_bips', FILTER_SANITIZE_NUMBER_INT);
    $montant_loyer = filter_input(INPUT_POST, 'montant_loyer', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $montant_charges = filter_input(INPUT_POST, 'montant_charges', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $total_mensuel = filter_input(INPUT_POST, 'total_mensuel', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $prorata = filter_input(INPUT_POST, 'prorata', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Convertir les montants en lettres
    $montant_loyer_lettres = convertNumberToWords($montant_loyer);
    $montant_charges_lettres = convertNumberToWords($montant_charges);
    $total_mensuel_lettres = convertNumberToWords($total_mensuel);
    $prorata_lettres = convertNumberToWords($prorata);
    $caution_lettres = convertNumberToWords($caution);

    try {
        $stmt = $db->prepare("INSERT INTO baux (locataire_id, garage_id, fait_le, fait_a, date_debut, duree, nombre_de_clefs, nombre_de_bips, montant_loyer, montant_charges, total_mensuel, prorata, montant_loyer_lettres, montant_charges_lettres, total_mensuel_lettres, prorata_lettres, caution, caution_lettres) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $params = [$locataire_id, $garage_id, $fait_le, $fait_a, $date_debut, $duree, $nombre_de_clefs, $nombre_de_bips, $montant_loyer, $montant_charges, $total_mensuel, $prorata, $montant_loyer_lettres, $montant_charges_lettres, $total_mensuel_lettres, $prorata_lettres, $caution, $caution_lettres];

        if ($stmt->execute($params)) {
             $bail_id = $db->lastInsertId(); // Récupère l'ID du bail nouvellement créé
             header("Location: confirmation.php?id=" . $bail_id);
             exit();
        } else {
            $message = "Erreur: " . $stmt->errorInfo()[2];
        }
    } catch (PDOException $e) {
        $message = "Erreur PDO: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Bail</title>
    <link rel="stylesheet" href="../css/form.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script>
        const garages = <?= json_encode($garages); ?>;

        function updateGarageInfo() {
            const garageId = document.getElementById('garage_id').value;
            const garage = garages.find(g => g.garage_id == garageId);
            if (garage) {
                const loyer = parseFloat(garage.loyer_hors_charge);
                const charges = parseFloat(garage.charge);
                const totalMensuel = loyer + charges;
                const caution = parseFloat(garage.caution);
                document.getElementById('montant_loyer').value = loyer.toFixed(2);
                document.getElementById('montant_charges').value = charges.toFixed(2);
                document.getElementById('total_mensuel').value = totalMensuel.toFixed(2);
                document.getElementById('caution').value = caution.toFixed(2); 
                document.getElementById('fait_a').value = garage.ville; // Pré-remplir le champ "Fait à"
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const montantLoyerInput = document.getElementById('montant_loyer');
            const montantChargesInput = document.getElementById('montant_charges');
            const montantCautionInput = document.getElementById('caution');
            const totalMensuelInput = document.getElementById('total_mensuel');
            const prorataInput = document.getElementById('prorata');
            const dateDebutInput = document.getElementById('date_debut');

            function updateCalculations() {
                const montantLoyer = parseFloat(montantLoyerInput.value) || 0;
                const montantCharges = parseFloat(montantChargesInput.value) || 0;
                const montantCaution = parseFloat(montantCautionInput.value) || 0;
                const totalMensuel = montantLoyer + montantCharges;

                // Calculer le prorata en fonction de la date de début
                const debutDate = new Date(dateDebutInput.value);
                const today = new Date();
                const daysInMonth = new Date(debutDate.getFullYear(), debutDate.getMonth() + 1, 0).getDate(); // Nombre de jours dans le mois de début
                const daysLeftInMonth = daysInMonth - debutDate.getDate() + 1; // Jours restants dans le mois

                let prorata;
          
                prorata = totalMensuel * (daysLeftInMonth / daysInMonth);

                totalMensuelInput.value = totalMensuel.toFixed(2);
                prorataInput.value = prorata.toFixed(2);
            }

            montantLoyerInput.addEventListener('input', updateCalculations);
            montantChargesInput.addEventListener('input', updateCalculations);
            montantCautionInput.addEventListener('input', updateCalculations);
            dateDebutInput.addEventListener('change', updateCalculations); // Écouter le changement de date de début
        });
    </script>

</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li>
                    <a href="proprietaire_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a>
                    <ul>
                        <li><a href="garage.php" title="Gérer vos garages"><i class="fas fa-warehouse"></i> Garages</a></li>
                        <li><a href="locataires.php" title="Gérer vos locataires"><i class="fas fa-users"></i> Locataires</a></li>
                        <li><a href="liste_baux.php" title="Gérer les baux"><i class="fas fa-file-contract"></i> Baux</a></li>
                        <li><a href="liste_paiements.php" title="Gérer les paiements"><i class="fas fa-money-check-alt"></i> Paiements</a></li>
                    </ul>
                </li>
                <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>

        <a href="liste_baux.php"><i class="fas fa-arrow-left"></i> Retour à la liste des baux</a><br><br>
        <h2><i class="fas fa-file-contract"></i> Créer un Bail</h2>

        <?php if (!empty($message)) : ?>
            <p class="success-message"><i class="fas fa-check-circle"></i> <?= $message ?></p>
        <?php endif; ?>

        <form action="bail.php" method="post">
			
			<div class="flex-container">
            <div>
                <label for="locataire_id"><i class="fas fa-user"></i> Locataire*:</label>
                <select id="locataire_id" name="locataire_id" required>
                    <option value="">Sélectionner un locataire...</option>
                    <?php foreach ($locataires as $locataire): ?>
                        <option value="<?= $locataire['locataire_id'] ?>">
                            <?= htmlspecialchars($locataire['nom']) ?> <?= htmlspecialchars($locataire['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <a href="form_locataire.php" target="_blank"><i class="fas fa-plus-circle"></i> Créer un nouveau locataire</a>
            </div>
			</div>

            <div>
                <label for="garage_id"><i class="fas fa-warehouse"></i> Garage*:</label>
                <select id="garage_id" name="garage_id" required onchange="updateGarageInfo()">
                    <option value="">Sélectionner un garage...</option>
                    <?php foreach ($garages as $garage): ?>
                        <option value="<?= $garage['garage_id'] ?>">
                            <?= htmlspecialchars($garage['addresse']) ?> - <?= htmlspecialchars($garage['numero_garage']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <a href="ajout_garage.php" target="_blank"><i class="fas fa-plus-circle"></i> Créer un nouveau garage</a>
            </div><br>

            <div class="flex-container">
                <div>
                    <label for="fait_a"><i class="fas fa-map-marker-alt"></i> Fait à*:</label>
                    <input type="text" id="fait_a" name="fait_a" required>
                </div>
                <div>
                    <label for="fait_le"><i class="fas fa-calendar-day"></i> Fait le*:</label>
                    <input type="date" id="fait_le" name="fait_le" required>
                </div>
                <div>
                    <label for="date_debut"><i class="fas fa-calendar-alt"></i> Début*:</label>
                    <input type="date" id="date_debut" name="date_debut" required>
                </div>
            </div>

            <div class="flex-container">
                <div>
                    <label for="duree"><i class="fas fa-hourglass-half"></i> Durée (en mois):</label>
                    <input type="number" id="duree" name="duree" value="12">
                </div>
                <div>
                    <label for="nombre_de_clefs"><i class="fas fa-key"></i> Nb Clés*:</label>
                    <input type="number" id="nombre_de_clefs" name="nombre_de_clefs" required>
                </div>
                <div>
                    <label for="nombre_de_bips"><i class="fas fa-remote"></i> Nb Bips*:</label>
                    <input type="number" id="nombre_de_bips" name="nombre_de_bips" required>
                </div>
            </div>

            <div class="flex-container">
                <div>
                    <label for="montant_loyer"><i class="fas fa-money-bill-wave"></i> Montant du Loyer (sans charges)*:</label>
                    <input type="text" id="montant_loyer" name="montant_loyer">
                </div>
                <div>
                    <label for="montant_charges"><i class="fas fa-coins"></i> Montant des Charges*:</label>
                    <input type="text" id="montant_charges" name="montant_charges">
                </div>
            </div>

            <div>
                <label for="total_mensuel"><i class="fas fa-calculator"></i> Total Mensuel*:</label>
                <input type="text" id="total_mensuel" name="total_mensuel" readonly>
            </div>

            <div>
                <label for="caution"><i class="fas fa-piggy-bank"></i> Caution (en chiffres)*:</label>
                <input type="number" id="caution" name="caution" required>
            </div>

            <div>
                <label for="prorata"><i class="fas fa-percentage"></i> Loyer du premier mois (au prorata)*:</label>
                <input type="text" id="prorata" name="prorata" readonly>
            </div>

            <div class="flex-container">
                <input type="submit" value="Créer le Bail">
                <button type="button" onclick="window.location.href = 'liste_baux.php';"><i class="fas fa-times"></i> Annuler</button>
            </div>
        </form>
    </div>
</body>
</html>