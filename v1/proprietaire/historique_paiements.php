<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et s'il a le rôle de propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: ../proprietaire/connexion_proprietaire.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer l'ID du propriétaire connecté
$stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
$stmt->execute([$user_id]);
$proprietaire_id = $stmt->fetchColumn();

if (!$proprietaire_id) {
    echo "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
    exit();
}

// Gestion des filtres de dates
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$query = "
    SELECT p.paiement_id, p.bail_id, p.statut, p.montant, p.methode, p.date, l.nom AS locataire_nom, l.prenom AS locataire_prenom, g.addresse, g.numero_garage
    FROM paiements p
    JOIN baux b ON p.bail_id = b.bail_id
    JOIN locataires l ON b.locataire_id = l.locataire_id
    JOIN garages g ON b.garage_id = g.garage_id
    WHERE p.statut = 'payé' AND g.proprietaire_id = ?
";

$params = [$proprietaire_id];

if (!empty($start_date)) {
    $query .= " AND p.date >= ?";
    $params[] = $start_date;
}

if (!empty($end_date)) {
    $query .= " AND p.date <= ?";
    $params[] = $end_date;
}

$query .= " ORDER BY p.date DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Paiements</title>
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        <h2><i class="fas fa-history"></i> Historique des Paiements</h2>
        <form method="GET" action="">
            <label for="start_date"><i class="fas fa-calendar-alt"></i> Date de début :</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            <label for="end_date"><i class="fas fa-calendar-alt"></i> Date de fin :</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            <button type="submit"><i class="fas fa-filter"></i> Filtrer</button>
            <button type="button" onclick="window.location.href='<?= basename($_SERVER['PHP_SELF']); ?>'"><i class="fas fa-sync-alt"></i> Réinitialiser</button>
        </form>

        <div class="container-paiements">
            <?php if (count($paiements) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Locataire</th>
                            <th><i class="fas fa-warehouse"></i> Num Garage</th>
                            <th><i class="fas fa-euro-sign"></i> Montant</th>
                            <th><i class="fas fa-calendar-day"></i> Date</th>
                            <th><i class="fas fa-credit-card"></i> Méthode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paiements as $paiement): ?>
                            <tr>
                                <td><?= htmlspecialchars($paiement['locataire_nom'] . ' ' . $paiement['locataire_prenom']) ?></td>
                                <td><?= htmlspecialchars($paiement['numero_garage']) ?></td>
                                <td><?= htmlspecialchars($paiement['montant']) ?> €</td>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($paiement['date']))) ?></td>
                                <td><?= htmlspecialchars($paiement['methode']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <form method="GET" action="export_paiements_csv_proprio.php">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
                    <button type="submit"><i class="fas fa-file-export"></i> Export CSV</button>
					
                </form>
			<a href="javascript:history.go(-1)"><i class="fas fa-arrow-left"></i> Retour</a>
            <?php else: ?>s
                <p><i class="fas fa-info-circle"></i> Aucun paiement trouvé pour cette période.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

