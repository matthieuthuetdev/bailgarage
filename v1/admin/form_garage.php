<?php
session_start();
include_once '../includes/db.php';

// Vérification de la connexion en tant qu'admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: connexion_admin.php");
    exit();
}

// Récupérer l'ID du propriétaire depuis l'URL
if (!isset($_GET['proprietaire_id'])) {
    die('Propriétaire ID non spécifié.');
}
$proprietaire_id = intval($_GET['proprietaire_id']);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adresse = $_POST['adresse'];
    $complement = $_POST['complement'];
    $CP = $_POST['CP'];
    $ville = $_POST['ville'];
    $pays = $_POST['pays'];
    $numero_garage = $_POST['numero_garage'];
    $numero_lot = $_POST['numero_lot'];
    $loyer_hors_charge = $_POST['loyer_hors_charge'];
    $charge = $_POST['charge'];
    $surface = $_POST['surface'];
    $commentaire = $_POST['commentaire'];
    $piece_jointe_path = $_POST['piece_jointe_path'];
    $piece_jointe_type = $_POST['piece_jointe_type'];
    $piece_jointe_nom = $_POST['piece_jointe_nom'];
    $syndic = $_POST['syndic'];
    $caution = $_POST['caution'];

    // Requête SQL pour insérer un nouveau garage
    $sql_insert = "
        INSERT INTO garages (proprietaire_id, addresse, complement, CP, ville, pays, numero_garage, numero_lot, loyer_hors_charge, charge, surface, commentaire, piece_jointe_path, piece_jointe_type, piece_jointe_nom, syndic, caution)
        VALUES (:proprietaire_id, :adresse, :complement, :CP, :ville, :pays, :numero_garage, :numero_lot, :loyer_hors_charge, :charge, :surface, :commentaire, :piece_jointe_path, :piece_jointe_type, :piece_jointe_nom, :syndic, :caution)
    ";
    $stmt_insert = $db->prepare($sql_insert);
    $stmt_insert->bindParam(':proprietaire_id', $proprietaire_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $stmt_insert->bindParam(':complement', $complement, PDO::PARAM_STR);
    $stmt_insert->bindParam(':CP', $CP, PDO::PARAM_STR);
    $stmt_insert->bindParam(':ville', $ville, PDO::PARAM_STR);
    $stmt_insert->bindParam(':pays', $pays, PDO::PARAM_STR);
    $stmt_insert->bindParam(':numero_garage', $numero_garage, PDO::PARAM_STR);
    $stmt_insert->bindParam(':numero_lot', $numero_lot, PDO::PARAM_STR);
    $stmt_insert->bindParam(':loyer_hors_charge', $loyer_hors_charge, PDO::PARAM_STR);
    $stmt_insert->bindParam(':charge', $charge, PDO::PARAM_STR);
    $stmt_insert->bindParam(':surface', $surface, PDO::PARAM_STR);
    $stmt_insert->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
    $stmt_insert->bindParam(':piece_jointe_path', $piece_jointe_path, PDO::PARAM_STR);
    $stmt_insert->bindParam(':piece_jointe_type', $piece_jointe_type, PDO::PARAM_STR);
    $stmt_insert->bindParam(':piece_jointe_nom', $piece_jointe_nom, PDO::PARAM_STR);
    $stmt_insert->bindParam(':syndic', $syndic, PDO::PARAM_STR);
    $stmt_insert->bindParam(':caution', $caution, PDO::PARAM_INT);

    if ($stmt_insert->execute()) {
        $_SESSION['message'] = "Garage ajouté avec succès.";
        header("Location: garages.php?proprietaire_id=" . $proprietaire_id);
        exit();
    } else {
        $_SESSION['message'] = "Erreur lors de l'ajout du garage.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Ajout de Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
        .icon-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5em;
            position: relative;
        }
        .icon-button .tooltip {
            visibility: hidden;
            width: 150px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 3px 0;
            position: absolute;
            z-index: 1;
            top: 125%; /* Position below the icon */
            left: 50%;
            margin-left: -75px; /* Center the tooltip */
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8em;
        }
        .icon-button .tooltip::after {
            content: "";
            position: absolute;
            bottom: 100%; /* Top of the tooltip */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent rgba(0, 0, 0, 0.7) transparent;
        }
        .icon-button:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="../includes/deconnexion.php">Déconnexion</a></li>
            <li><a href="admin_dash.php">Accueil</a></li>
        </ul>
    </nav>
    <a href="garages.php?proprietaire_id=<?php echo htmlspecialchars($proprietaire_id); ?>">Retour à la liste des garages</a>
    <h1>Formulaire d'Ajout de Garage</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <span class="<?php echo isset($_SESSION['email_sent']) && $_SESSION['email_sent'] ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </span>
        <?php
            unset($_SESSION['message']);
            if (isset($_SESSION['email_sent'])) unset($_SESSION['email_sent']);
        ?>
    <?php endif; ?>

    <form action="" method="post">
        <input type="hidden" name="proprietaire_id" value="<?php echo htmlspecialchars($proprietaire_id); ?>">

        <label for="adresse">Adresse :</label>
        <input type="text" id="adresse" name="adresse" required><br>

        <label for="complement">Complément d'adresse :</label>
        <input type="text" id="complement" name="complement" required><br>

        <label for="CP">Code Postal :</label>
        <input type="text" id="CP" name="CP" required><br>

        <label for="ville">Ville :</label>
        <input type="text" id="ville" name="ville" required><br>

        <label for="pays">Pays :</label>
        <input type="text" id="pays" name="pays" required><br>

        <label for="numero_garage">Numéro de Garage :</label>
        <input type="text" id="numero_garage" name="numero_garage" required><br>

        <label for="numero_lot">Numéro de Lot :</label>
        <input type="text" id="numero_lot" name="numero_lot" required><br>

        <label for="loyer_hors_charge">Loyer Hors Charges :</label>
        <input type="text" id="loyer_hors_charge" name="loyer_hors_charge" required><br>

        <label for="charge">Charges :</label>
        <input type="text" id="charge" name="charge" required><br>

        <label for="surface">Surface :</label>
        <input type="text" id="surface" name="surface"><br>

        <label for="commentaire">Commentaire :</label>
        <textarea id="commentaire" name="commentaire"></textarea><br>

        <label for="piece_jointe_path">Chemin de la Pièce Jointe :</label>
        <input type="text" id="piece_jointe_path" name="piece_jointe_path"><br>

        <label for="piece_jointe_type">Type de la Pièce Jointe :</label>
        <input type="text" id="piece_jointe_type" name="piece_jointe_type"><br>

        <label for="piece_jointe_nom">Nom de la Pièce Jointe :</label>
        <input type="text" id="piece_jointe_nom" name="piece_jointe_nom"><br>

        <label for="syndic">Syndic :</label>
        <textarea id="syndic" name="syndic" required></textarea><br>

        <label for="caution">Caution :</label>
        <input type="number" id="caution" name="caution" required><br>

        <button type="submit">Ajouter le Garage</button>
    </form>
</body>
</html>
