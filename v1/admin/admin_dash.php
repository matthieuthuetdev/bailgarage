<?php
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/app.bailgarage.fr/error_log'); // Chemin vers le fichier de log des erreurs

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: connexion_admin.php"); // Redirige s'il n'est pas connecté en tant qu'admin
    exit();
}


// Récupérer le nom de l'administrateur pour l'afficher dynamiquement
$user_id = $_SESSION['user_id'];
$sql = "SELECT nom, prenom FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification rapide pour s'assurer que l'utilisateur existe
if (!$user) {
    $_SESSION['error_message'] = "Utilisateur non trouvé.";
    header("Location: ../connexion_admin.php");
    exit();
}


$welcome_message = "Bienvenue, " . $user['prenom'] . " " . htmlspecialchars($user['nom']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil Administrateur</title>
    <link rel="stylesheet" href="../css/table.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
	
    <div class="container">
		<nav>
            <ul> 
                 <li><a href="admin_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a></li>
				<li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
            	<li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>
		
		<div class="title">
        <h2><?php echo $welcome_message; ?></h2>
	</div>
		<div class="link-container">
			 <a href="ajout_proprietaire.php">Ajouter un Propriétaire</a>
            <a href="liste_proprietaires.php">Liste des Propriétaires</a>
		</div>
    </div>
			
</body>
</html>
