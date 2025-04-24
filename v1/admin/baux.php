<?php
session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php");
    exit();
}

// Récupérer l'ID du propriétaire depuis l'URL
if (!isset($_GET['proprietaire_id'])) {
    die('Propriétaire ID non spécifié.');
}
$proprietaire_id = intval($_GET['proprietaire_id']);

// Récupérer le nom de l'utilisateur pour l'afficher dynamiquement
$sql_user = "SELECT nom, prenom FROM users WHERE user_id = :user_id";
$stmt_user = $db->prepare($sql_user);
$stmt_user->bindParam(':user_id', $_SESSION['user_id']);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Vérification rapide pour s'assurer que l'utilisateur existe
if (!$user) {
    $_SESSION['error_message'] = "Utilisateur non trouvé.";
    header("Location: ../admin/connexion_admin.php");
    exit();
}

$welcome_message = "Bienvenue, " . $user['nom'] . " " . $user['prenom'];

// Récupérer les informations des baux du propriétaire spécifié
$sql_baux = "SELECT b.bail_id, l.nom AS locataire_nom, l.prenom AS locataire_prenom, g.addresse, g.numero_garage, b.date_debut, b.date_fin, b.status 
             FROM baux b
             JOIN garages g ON b.garage_id = g.garage_id
             JOIN locataires l ON b.locataire_id = l.locataire_id
             WHERE g.proprietaire_id = :proprietaire_id";
$stmt_baux = $db->prepare($sql_baux);
$stmt_baux->bindParam(':proprietaire_id', $proprietaire_id);
$stmt_baux->execute();
$baux = $stmt_baux->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Baux</title>
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container">
        <nav>
            <ul> 
                <li>
                    <a href="admin_dash.php" title="Retour à l'accueil">Accueil</a>
                    <ul>
                        <li><a href="garages.php" title="Gérer les garages">Garages</a></li>
                        <li><a href="locataires.php" title="Gérer les locataires">Locataires</a></li>
                        <li><a href="liste_baux.php" title="Gérer les baux">Bails</a></li>
                        <li><a href="liste_paiements.php" title="Gérer les paiements">Paiements</a></li>
                    </ul>
                </li>
                <li><a href="profil.php" title="Voir et modifier votre profil">Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte">Déconnexion</a></li>  
            </ul>
        </nav>
        
        <div class="container-baux">
            <h3>Liste des Baux</h3>
            <a href="bail.php">Créer un Bail</a><br><br>
            <input type="checkbox" id="showActiveOnly" checked> Afficher uniquement les baux en cours
            <?php if (count($baux) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Locataire</th>
                            <th>N° Garage</th>
                            <th>Date de Début</th>
                            <th>Date de Fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                            <th>Docs</th>
                        </tr>
                    </thead>
                    <tbody id="bauxTable">
                        <?php foreach ($baux as $bail): 
                            $date_debut = !empty($bail['date_debut']) ? (new DateTime($bail['date_debut']))->format('d-m-Y') : '';
                            $date_fin = !empty($bail['date_fin']) ? (new DateTime($bail['date_fin']))->format('d-m-Y') : '';
                        ?>
                            <tr class="bailRow" data-status="<?php echo htmlspecialchars($bail['status']); ?>">
                                <td><?php echo htmlspecialchars($bail['locataire_nom'] . ' ' . $bail['locataire_prenom']); ?></td>
                                <td><?php echo htmlspecialchars($bail['numero_garage']); ?></td>
                                <td><?php echo htmlspecialchars($date_debut); ?></td>
                                <td><?php echo htmlspecialchars($date_fin); ?></td>
                                
                                <td><?php if ($bail['status'] == 'active'): ?>
                                        <i class="fas fa-circle" style="color: green;"></i>
                                    <?php else: ?>
                                        <i class="fas fa-circle" style="color: red;"></i>
                                    <?php endif; ?>
                                </td>
                                
                                <td><a href="confirmation.php?id=<?php echo $bail['bail_id']; ?>" title="Modifier le bail" target="_blank"><i class="fas fa-pencil-alt"></i></a>
                                    <?php if ($bail['status'] == 'active'): ?>
                                        <a href="terminer_bail_form.php?terminate_bail=true&bail_id=<?php echo $bail['bail_id']; ?>">Mettre fin au bail</a>
                                        <a href="supprimer_bail.php?delete_bail=true&bail_id=<?php echo $bail['bail_id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bail ?');" title="Supprimer le bail"><i class="fas fa-trash-alt" style="color: red;"></i></a>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <a href="docs_bail.php?id=<?php echo $bail['bail_id']; ?>" title="Cliquez ici pour voir les documents relatifs au bail" target="_blank"><i class="fas fa-folder"></i> </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun bail trouvé.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('showActiveOnly').addEventListener('change', function() {
            var showActiveOnly = this.checked;
            var rows = document.querySelectorAll('.bailRow');
            rows.forEach(function(row) {
                if (showActiveOnly) {
                    if (row.getAttribute('data-status') === 'active') {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                } else {
                    row.style.display = '';
                }
            });
        });

        // Trigger the change event on page load to apply the filter
        document.getElementById('showActiveOnly').dispatchEvent(new Event('change'));
    </script>
</body>
</html>

