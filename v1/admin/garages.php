<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../admin/connexion_admin.php"); // Redirige s'il n'est pas connecté en tant qu'admin
    exit();
}

// Récupérer l'ID du propriétaire depuis l'URL
if (!isset($_GET['proprietaire_id'])) {
    die('Propriétaire ID non spécifié.');
}
$proprietaire_id = intval($_GET['proprietaire_id']);

// Récupérer les informations du propriétaire
$stmt = $db->prepare("SELECT nom, prenom FROM proprietaires WHERE proprietaire_id = ?");
$stmt->execute([$proprietaire_id]);
$proprietaire = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Vérification que le proprietaire_id a été trouvé
if (!$proprietaire) {
    echo "Erreur: L'ID du propriétaire n'a pas été trouvé.";
    exit();
}

// Gestion de la recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Construire la requête SQL avec le filtre de statut
$sql_garages = "SELECT g.garage_id, g.addresse, g.numero_lot, g.numero_garage, g.pays, g.ville, g.piece_jointe_path, g.complement, g.CP, g.loyer_hors_charge, g.charge, g.surface, g.commentaire, g.piece_jointe_type, g.piece_jointe_nom, g.syndic, g.caution, g.reference,
                (SELECT COUNT(*) FROM baux b WHERE b.garage_id = g.garage_id) AS est_loue,
                (SELECT b.bail_id FROM baux b WHERE b.garage_id = g.garage_id LIMIT 1) AS bail_id,
                (SELECT l.nom FROM baux b
                 JOIN locataires l ON b.locataire_id = l.locataire_id
                 WHERE b.garage_id = g.garage_id LIMIT 1) AS locataire_nom
                FROM garages g
                WHERE g.proprietaire_id = :proprietaire_id
                  AND (g.addresse LIKE :search OR g.ville LIKE :search OR g.numero_garage LIKE :search)";

// Ajouter des conditions supplémentaires en fonction du statut
if ($status === 'rented') {
    $sql_garages .= " AND (SELECT COUNT(*) FROM baux b WHERE b.garage_id = g.garage_id) > 0";
} elseif ($status === 'not_rented') {
    $sql_garages .= " AND (SELECT COUNT(*) FROM baux b WHERE b.garage_id = g.garage_id) = 0";
}

$stmt_garages = $db->prepare($sql_garages);
$search_param = '%' . $search . '%';
$stmt_garages->bindParam(':proprietaire_id', $proprietaire_id, PDO::PARAM_INT);
$stmt_garages->bindParam(':search', $search_param, PDO::PARAM_STR);
$stmt_garages->execute();
$garages = $stmt_garages->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Garages</title>
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container">
        <nav>
            <ul> 
                <li>
                    <a href="admin_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a>
                </li>
                <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>  
            </ul>
        </nav>
		
        <div class="container-garages">
            <h3 title="Liste des garages que vous possédez">Liste des <?php echo count($garages); ?> Garages</h3>
            <a href="ajout_garage.php?proprietaire_id=<?php echo $proprietaire_id; ?>" title="Ajouter un nouveau garage"><i class="fas fa-plus"></i>Ajouter un garage</a><br><br>


            <form method="GET" action="" title="Rechercher des garages par adresse, ville ou numéro">
                <label>Effectuer une recherche sur l'adresse, la ville ou le numéro du garage:</label><br>
                <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
                <div class="search-buttons">
                    <button type="submit" title="Lancer la recherche">Rechercher</button>
                    <button type="button" onclick="window.location.href='?reset=1'" title="Réinitialiser la recherche">Réinitialiser</button>
                </div>
                <div class="radio-group" style="margin-top: 20px;">
                    <label for="status">Filtrer par statut:</label>
                    <input type="radio" id="all" name="status" value="all" <?php echo $status === 'all' ? 'checked' : ''; ?> onchange="this.form.submit();" title="Voir tous les garages">
                    <label for="all">Tout</label>
                    <input type="radio" id="rented" name="status" value="rented" <?php echo $status === 'rented' ? 'checked' : ''; ?> onchange="this.form.submit();" title="Voir uniquement les garages loués">
                    <label for="rented">Loué</label>
                    <input type="radio" id="not_rented" name="status" value="not_rented" <?php echo $status === 'not_rented' ? 'checked' : ''; ?> onchange="this.form.submit();" title="Voir uniquement les garages non loués">
                    <label for="not_rented">Non Loué</label>
                </div>
            </form>

            <?php if (count($garages) > 0): ?>
                <table title="Tableau listant les garages">
                    <thead>
                        <tr>
                            <th title="Statut de location du garage">Loué</th>
                            <th title="Numéro du garage">Numéro de Garage</th>
                            <th title="Adresse du garage">Adresse</th>
                            <th title="Ville où se trouve le garage">Ville</th>
                            <th title="Nom du locataire">Locataire</th>
                            <th title="Actions disponibles pour le garage" style="display: flex; justify-content: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($garages as $garage): ?>
                            <tr>
                                <td title="<?php echo $garage['est_loue'] > 0 ? 'Ce garage est loué' : 'Ce garage n\'est pas loué'; ?>">
                                    <?php if ($garage['est_loue'] > 0): ?>
                                        <span style="color: green;">✔</span>
                                    <?php else: ?>
                                        <span style="color: red;">✘</span>
                                    <?php endif; ?>
                                </td>
                                <td title="Numéro unique du garage"><?php echo htmlspecialchars($garage['numero_garage']); ?></td>
                                <td title="Adresse complète du garage"><?php echo htmlspecialchars($garage['addresse']); ?></td>
                                <td title="Ville où se trouve le garage"><?php echo htmlspecialchars($garage['ville']); ?></td>
                                <td title="Nom du locataire actuel">
                                    <?php if ($garage['bail_id']): ?>
                                        <a href="locataires.php?id=<?php echo $garage['bail_id']; ?>" title="Voir les détails du locataire">
                                            <?php echo htmlspecialchars($garage['locataire_nom']); ?>
                                        </a>
                                    <?php else: ?>
                                        Aucun
                                    <?php endif; ?>
                                </td>
                               <td class="action-icons" title="Actions disponibles pour ce garage" style="text-align: center;">
	  <div style="display: flex; justify-content: center;">
                                    <a style="margin-right: 10px;" href="modifier_garage.php?id=<?php echo $garage['garage_id']; ?>&proprietaire_id=<?php echo $proprietaire_id; ?>" title="Modifier ce garage"><i class="fas fa-pencil-alt"></i></a>

                                    <a style="margin-right: 10px;" href="supprimer_garage.php?id=<?php echo $garage['garage_id']; ?>&proprietaire_id=<?php echo $proprietaire_id; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce garage ?');" title="Supprimer ce garage"><i class="fas fa-trash-alt" style="color: red;"></i></a>
									
                                    <a style="margin-right: 10px;" href="dupliquer_garage.php?id=<?php echo $garage['garage_id']; ?>&proprietaire_id=<?php echo $proprietaire_id; ?>" title="Dupliquer ce garage"><i class="fas fa-copy"></i></a>
								   
								   <a href="#" onclick="openModal(<?php echo htmlspecialchars(json_encode($garage)); ?>)" title="Voir les détails du garage"><i class="fas fa-search"></i></a>
								   </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p title="Aucun garage trouvé pour les critères de recherche actuels">Aucun garage trouvé.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal HTML -->
    <div id="garageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
			<i class="fas fa-warehouse" style="font-size: 24px; color: #213c4a; margin-bottom: 10px;"></i>
            <h2>Détails du Garage</h2>
            <p id="garageDetails"></p>
        </div>
    </div>

    <script>
    // Get the modal
    var modal = document.getElementById("garageModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Function to open the modal
    function openModal(garageDetails) {
        let details = `
            <strong>Numéro de Garage:</strong> ${garageDetails.numero_garage}<br>
            <strong>Adresse:</strong> ${garageDetails.addresse}<br>
            <strong>Complément:</strong> ${garageDetails.complement}<br>
            <strong>Code Postal:</strong> ${garageDetails.CP}<br>
            <strong>Ville:</strong> ${garageDetails.ville}<br>
            <strong>Pays:</strong> ${garageDetails.pays}<br>
            <strong>Numéro de Lot:</strong> ${garageDetails.numero_lot}<br>
            <strong>Loyer Hors Charge:</strong> ${garageDetails.loyer_hors_charge}<br>
            <strong>Charge:</strong> ${garageDetails.charge}<br>
            <strong>Surface:</strong> ${garageDetails.surface ? garageDetails.surface + ' m²' : 'N/A'}<br>
            <strong>Commentaire:</strong> ${garageDetails.commentaire ? garageDetails.commentaire : 'N/A'}<br>
            <strong>Syndic:</strong> ${garageDetails.syndic}<br>
            <strong>Caution:</strong> ${garageDetails.caution}<br>
			<strong>Référence de virement:</strong> ${garageDetails.reference}<br>
			<strong>Pièce Jointe:</strong> <a href="piece_jointe.php?id=${garageDetails.garage_id}" target="_blank">${garageDetails.piece_jointe_nom}</a><br>
        `;
        document.getElementById('garageDetails').innerHTML = details;
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>
</html>
