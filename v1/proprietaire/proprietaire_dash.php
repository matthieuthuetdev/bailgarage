<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifiez que l'utilisateur est connecté et est un proprietaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php"); // Redirige s'il n'est pas connecté en tant que proprio
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer le nom de l'utilisateur pour l'afficher dynamiquement
$sql = "SELECT nom, prenom FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification rapide pour s'assurer que l'utilisateur existe
if (!$user) {
    $_SESSION['error_message'] = "Utilisateur non trouvé.";
    header("Location: ../connexion_proprietaire.php");
    exit();
}

// Message bienvenue dynamique
$welcome_message = "Bienvenue, " . htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil Proprietaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/table.css">
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
        
        <h2><?php echo $welcome_message; ?></h2>
        <div class="link-container">
            <div><a href="garage.php" title="Gérer vos garages"><i class="fas fa-warehouse"></i> Voir les Garages</a></div>
            <div><a href="locataires.php" title="Gérer vos locataires"><i class="fas fa-users"></i> Voir les Locataires</a></div>
            <div><a href="liste_baux.php" title="Gérer les baux"><i class="fas fa-file-contract"></i> Voir les Baux</a></div>
            <div><a href="liste_paiements.php" title="Gérer les paiements"><i class="fas fa-money-check-alt"></i> Voir les Paiements</a></div>
        </div>
    </div>
</body>
</html>
