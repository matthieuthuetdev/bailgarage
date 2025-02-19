<?php
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/app.bailgarage.fr/error_log'); // Chemin vers le fichier de log des erreurs


session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php"); // Redirige s'il n'est pas connecté en tant qu'admin
    exit();
}

// Sélectionnez tous les propriétaires de la base de données
$sql = "SELECT *
        FROM proprietaires
        INNER JOIN users ON proprietaires.user_id = users.user_id";
$stmt = $db->prepare($sql);
$stmt->execute();
$proprietaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Propriétaires</title>
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
        <h2>Liste des Propriétaires</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Téléphone</th>
					<th>Email</th>
                    <th>Détails</th>
					<th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proprietaires as $proprietaire): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proprietaire['nom']); ?></td>
                        <td><?php echo htmlspecialchars($proprietaire['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($proprietaire['telephone']); ?></td>
						<td><?php echo htmlspecialchars($proprietaire['email']); ?></td>
                        <td><a href="details_proprietaire.php?id=<?php echo htmlspecialchars($proprietaire['proprietaire_id']); ?>" title="Voir les details du proprietaire"><i class="fas fa-search"></i></a></td>
						<td><a href="supprimer_proprietaire.php?id=<?= htmlspecialchars($proprietaire['proprietaire_id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce proprietaire ?');" title="Supprimer ce proprietaire"><i class="fas fa-trash-alt" style="color: red;"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
