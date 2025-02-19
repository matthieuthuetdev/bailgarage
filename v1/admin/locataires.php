<?php
ini_set('display_errors', 0); // Désactive l'affichage des erreurs
ini_set('log_errors', 1); // Active l'enregistrement des erreurs
ini_set('error_log', '../../logs/app.bailgarage.fr/error_log'); // Chemin vers le fichier de log des erreurs

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

// Récupérer le nom de l'utilisateur pour l'afficher dynamiquement
$sql = "SELECT nom, prenom FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification rapide pour s'assurer que l'utilisateur existe
if (!$user) {
    $_SESSION['error_message'] = "Utilisateur non trouvé.";
    header("Location: ../admin/connexion_admin.php");
    exit();
}

// Message de bienvenue dynamique
$welcome_message = "Bienvenue, " . htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8');

// Gestion de la recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Récupérer les informations des locataires avec les baux associés s'il y en a
$sql_locataires = "
    SELECT l.*, 
           IFNULL(GROUP_CONCAT(DISTINCT b.bail_id ORDER BY b.bail_id SEPARATOR ', '), 'Aucun') AS baux,
           GROUP_CONCAT(DISTINCT p.mois ORDER BY p.mois SEPARATOR ', ') AS paiements
    FROM locataires l
    LEFT JOIN baux b ON l.locataire_id = b.locataire_id
    LEFT JOIN paiements p ON b.bail_id = p.bail_id AND p.statut = 'payé'
    WHERE (l.nom LIKE :search OR l.prenom LIKE :search OR l.addresse LIKE :search OR l.ville LIKE :search)
      AND l.proprietaire_id = :proprietaire_id
    GROUP BY l.locataire_id";

$stmt_locataires = $db->prepare($sql_locataires);
$search_param = '%' . $search . '%';
$stmt_locataires->bindParam(':search', $search_param, PDO::PARAM_STR);
$stmt_locataires->bindParam(':proprietaire_id', $proprietaire_id, PDO::PARAM_INT);
$stmt_locataires->execute();
$locataires = $stmt_locataires->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Locataires</title>
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
        <div class="container-locataires">
            <h3>Liste des locataires</h3>
			
                <a href="ajout_locataire.php?proprietaire_id=<?= $proprietaire_id ?>"><i class="fas fa-plus"></i>Ajouter un locataire</a> <br><br>
            <form method="GET" action="">
                <label>Effectuer une recherche sur le nom, le prénom, l'adresse ou la ville du locataire:</label><br>
                <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">Rechercher</button>
                <button type="button" onclick="window.location.href='?reset=1'">Réinitialiser</button>
            </form>
            <br>
            <?php if (count($locataires) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>NOM</th>
                            <th>Prénom</th>
                            <th>Téléphone</th>
                            <th>Mail</th>
                            <th>Garages</th>
                            <th>Paiements</th>
                            <th>Pièce Identité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locataires as $locataire): ?>
                            <tr>
                                <td><?= htmlspecialchars($locataire['nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($locataire['prenom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a href="tel:<?= htmlspecialchars($locataire['telephone'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($locataire['telephone'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                                <td><a href="mailto:<?= htmlspecialchars($locataire['email'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($locataire['email'], ENT_QUOTES, 'UTF-8'); ?></a></td>
								
								
                                <td>
                                       <?php 
                                    if ($locataire['garages'] != 'Aucun') {
                                        $garage_ids = explode(', ', $locataire['garage_ids']);
                                        $garages = explode(', ', $locataire['garages']);
                                        foreach ($garage_ids as $index => $garage_id) {
                                            echo "<a href='garage.php?id=" . htmlspecialchars($garage_id, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($garages[$index], ENT_QUOTES, 'UTF-8') . "</a><br>";
                                        }
                                    } else {
                                        echo 'Aucun';
                                    }
                                    ?>
                                </td>
								
                                <td><a title="Voir les paiements passés" href='locataire_paiements.php?proprietaire_id=<?= htmlspecialchars($proprietaire['proprietaire_id'], ENT_QUOTES, 'UTF-8'); ?>&locataire_id=<?= htmlspecialchars($locataire['locataire_id'], ENT_QUOTES, 'UTF-8'); ?>'><i class="fas fa-credit-card"></i> Voir</a></td>


                                <td>
                                    <?php if (!empty($locataire['piece_jointe_path'])): ?>
                                        <a href='../proprietaire/piece_jointe_locataire.php?id=<?= htmlspecialchars($locataire['locataire_id'], ENT_QUOTES, 'UTF-8'); ?>' target="_blank">Voir la pièce jointe</a>
                                    <?php else: ?>
                                        <a href="ajout_pj.php?locataire_id=<?= htmlspecialchars($locataire['locataire_id'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="add-attachment">
                                            <i class="fas fa-plus"></i> Ajouter
                                        </a>
                                    <?php endif; ?>
                                </td>
								<td class="action-icons" title="Actions disponibles pour ce locataire" style="text-align: center;">
									<div style="display: flex; justify-content: center;">
                                    <a style="margin-right: 10px;" href="modifier_locataire.php?id=<?= htmlspecialchars($locataire['locataire_id'], ENT_QUOTES, 'UTF-8'); ?>" title="Modifier ce locataire"><i class="fas fa-pencil-alt"></i></a>
                                    <a style="margin-right: 10px;" href="supprimer_locataire.php?id=<?= htmlspecialchars($locataire['locataire_id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce locataire ?');" title="Supprimer ce locataire"><i class="fas fa-trash-alt" style="color: red;"></i></a>
                                    <a style="margin-right: 10px;" href="#" onclick="openModal(<?php echo htmlspecialchars(json_encode($locataire)); ?>)" title="Voir les détails du locataire"><i class="fas fa-search"></i></a>
									</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun locataire trouvé.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal HTML -->
    <div id="locataireModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
			<i class="fas fa-users" style="font-size: 24px; color: #213c4a; margin-bottom: 10px;"></i>
            <h2>Détails du Locataire</h2>
            <p id="locataireDetails"></p>
        </div>
    </div>

    <script>
    // Get the modal
    var modal = document.getElementById("locataireModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Function to open the modal
    function openModal(locataireDetails) {
        let details = `
			<strong>Genre:</strong> ${locataireDetails.genre}<br>
            <strong>Nom:</strong> ${locataireDetails.nom}<br>
            <strong>Prénom:</strong> ${locataireDetails.prenom}<br>
            <strong>Téléphone:</strong> ${locataireDetails.telephone}<br>
			<strong>Téléphone fixe:</strong> ${locataireDetails.fixe}<br>
            <strong>Email:</strong> ${locataireDetails.email}<br>
            <strong>Adresse:</strong> ${locataireDetails.addresse}<br>
			<strong>Complément:</strong> ${locataireDetails.complement}<br>
            <strong>Ville:</strong> ${locataireDetails.ville}<br>
            <strong>Code Postal:</strong> ${locataireDetails.CP}<br>
           <strong>RGPD:</strong> ${locataireDetails.rgpd}<br>
		   <strong>Quittance:</strong> ${locataireDetails.quittance}<br>
        `;
        document.getElementById('locataireDetails').innerHTML = details;
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
