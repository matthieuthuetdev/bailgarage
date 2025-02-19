<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php"); // Redirige s'il n'est pas connecté en tant qu'admin
    exit();
}

// Vérifiez que l'ID du propriétaire est fourni
if (!isset($_GET['id'])) {
    echo "Erreur : ID du propriétaire non spécifié.";
    exit();
}

$proprietaire_id = $_GET['id'];

// Récupérer les informations du propriétaire
$sql = "SELECT p.*, u.nom AS user_nom, u.prenom AS user_prenom
        FROM proprietaires p
        INNER JOIN users u ON p.user_id = u.user_id
        WHERE p.proprietaire_id = :proprietaire_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':proprietaire_id', $proprietaire_id, PDO::PARAM_INT);
$stmt->execute();
$proprietaire = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifiez que le propriétaire existe
if (!$proprietaire) {
    echo "Erreur : Propriétaire non trouvé.";
    exit();
}

// Message de bienvenue dynamique
$welcome_message = "Détails de " . htmlspecialchars($proprietaire['prenom']) . " " . htmlspecialchars($proprietaire['nom']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du Propriétaire</title>
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
        <h2><?php echo $welcome_message; ?></h2>
        <p><strong>Société:</strong> <?php echo htmlspecialchars($proprietaire['societe']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($proprietaire['email']); ?></p> <br>
		
        <div class="link-container">
			<div><a href="garages.php?proprietaire_id=<?php echo htmlspecialchars($proprietaire_id); ?>" title="Gérer vos garages"><i class="fas fa-warehouse"></i> Voir les Garages</a></div>
            <div><a href="locataires.php?proprietaire_id=<?php echo htmlspecialchars($proprietaire_id); ?>" title="Gérer vos locataires"><i class="fas fa-users"></i> Voir les Locataires</a></div>
            <div><a href="baux.php?proprietaire_id=<?php echo htmlspecialchars($proprietaire_id); ?>" title="Gérer les baux"><i class="fas fa-file-contract"></i> Voir les Baux</a></div>
            <div><a href="paiements.php?proprietaire_id=<?php echo htmlspecialchars($proprietaire_id); ?>" title="Gérer les paiements"><i class="fas fa-money-check-alt"></i> Voir les Paiements</a></div>
        </div>
		
    </div>
</body>
</html>
