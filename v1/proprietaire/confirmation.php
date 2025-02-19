<?php
ini_set('display_errors', 0); // Désactive l'affichage des erreurs
ini_set('log_errors', 1); // Active l'enregistrement des erreurs
ini_set('error_log', '../../logs/app.bailgarage.fr/error_log'); // Chemin vers le fichier de log des erreurs

include_once '../includes/db.php';
session_start();

// Check if user is logged in as 'proprietaire'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: ../connexion_proprietaire.php");
    exit();
}

// Fetch bail information
$bail_id = $_GET['id'] ?? null;
if (!$bail_id) {
    echo "Bail ID manquant.";
    exit();
}

$stmt = $db->prepare("SELECT * FROM baux WHERE bail_id = ?");
$stmt->bindParam(1, $bail_id, PDO::PARAM_INT);
$stmt->execute();
$bail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bail) {
    echo "Bail non trouvé.";
    exit();
}

// Fetch locataire information
$stmt_locataire = $db->prepare("SELECT nom, prenom, addresse, CP, ville, telephone, email FROM locataires WHERE locataire_id = ?");
$stmt_locataire->bindParam(1, $bail['locataire_id'], PDO::PARAM_INT);
$stmt_locataire->execute();
$locataire = $stmt_locataire->fetch(PDO::FETCH_ASSOC);

// Fetch garage information
$stmt_garage = $db->prepare("SELECT addresse, numero_garage, numero_lot, ville, CP, pays, surface FROM garages WHERE garage_id = ?");
$stmt_garage->bindParam(1, $bail['garage_id'], PDO::PARAM_INT);
$stmt_garage->execute();
$garage = $stmt_garage->fetch(PDO::FETCH_ASSOC);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/phpmailer/src/Exception.php';
require '../includes/phpmailer/src/PHPMailer.php';
require '../includes/phpmailer/src/SMTP.php';

if (isset($_POST["send_email"])) {
    try {
        $mail = new PHPMailer(true);
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lililizalopi2021@gmail.com'; // Change to your email
        $mail->Password = 'esbb phvk adqu zzgm'; // Change to your email password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        // Sender and recipient
        $mail->setFrom('lililizalopi2021@gmail.com', 'Admin BailGarage'); // Change to your email
        $mail->addAddress($locataire['email']);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Details du Bail';
        $mail->Body    = 'Bonjour,<br><br>Voici les détails de votre bail.<br><br>' . 
                         'Nom: ' . htmlspecialchars($locataire['nom']) . ' ' . htmlspecialchars($locataire['prenom']) . '<br>' . 
                         'Adresse: ' . htmlspecialchars($locataire['addresse']) . ', ' . htmlspecialchars($locataire['CP']) . ', ' . htmlspecialchars($locataire['ville']) . '<br>' . 
                         'Téléphone: ' . htmlspecialchars($locataire['telephone']) . '<br>' . 
                         'Email: ' . htmlspecialchars($locataire['email']) . '<br><br>' . 
                         'Informations Garage:<br>' . 
                         'Adresse: ' . htmlspecialchars($garage['addresse']) . ', ' . htmlspecialchars($garage['CP']) . ', ' . htmlspecialchars($garage['ville']) . ', ' . htmlspecialchars($garage['pays']) . '<br>' . 
                         'Numéro: ' . htmlspecialchars($garage['numero_garage']) . '<br>' . 
                         'Lot: ' . htmlspecialchars($garage['numero_lot']) . '<br>' . 
                         'Surface: ' . htmlspecialchars($garage['surface']) . '<br><br>' . 
                         'Informations Bail:<br>' . 
                         'Fait le: ' . htmlspecialchars($bail['fait_le']) . '<br>' . 
                         'Fait à: ' . htmlspecialchars($bail['fait_a']) . '<br>' . 
                         'Date de Début: ' . htmlspecialchars($bail['date_debut']) . '<br>' . 
                         'Durée: ' . htmlspecialchars($bail['duree']) . '<br>' . 
                         'Nombre de Clés: ' . htmlspecialchars($bail['nombre_de_clefs']) . '<br>' . 
                         'Nombre de Bips: ' . htmlspecialchars($bail['nombre_de_bips']) . '<br>' . 
                         'Montant du Loyer: ' . htmlspecialchars($bail['montant_loyer']) . '<br>' . 
                         'Montant des Charges: ' . htmlspecialchars($bail['montant_charges']) . '<br>' . 
                         'Total Mensuel: ' . htmlspecialchars($bail['total_mensuel']) . '<br>' . 
                         'Prorata/Jour: ' . htmlspecialchars($bail['prorata']) . '<br>' . 
                         'Caution: ' . htmlspecialchars($bail['caution']) . '<br><br>' . 
                         'Cordialement,<br>L\'équipe BailGarage';

        // Send email
        $mail->send();
        
        $email_sent = true;
    } catch (Exception $e) {
        $email_sent = false;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation du Bail</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and Global Styles */
        body, h2, p, form, input, a, textarea, label, button, ul, li, nav {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            color: #213c4a;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #e9eef1;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            border-radius: 0px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #002011;
        }

        h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #002011;
        }

        p {
            margin-bottom: 10px;
            font-size: 16px;
            color: #213c4a;
        }

        .actions {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #213c4a;
            color: white;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        button:hover {
            background-color: #002011;
        }

        .bail-details {
            text-align: left;
            margin-top: 20px;
        }

        .email-status {
            margin-top: 20px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
        }

        /* Styles pour le modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #ffffff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .flex-container {
            display: flex;
            gap: 15px;
        }

        label {
            font-size: 14px;
            color: #213c4a;
            text-align: left;
            display: block;
        }

        input[type="text"], input[type="date"], input[type="number"], textarea, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        input[type="submit"], button {
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #213c4a;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            width: 48%;
            text-align: center;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #002011;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-file-alt"></i> Confirmation du Bail</h2>
        <div class="actions">
            <button id="edit-button" onclick="toggleEditMode()"><i class="fas fa-edit"></i> Modifier les informations du bail</button>
            <button onclick="window.location.href='liste_baux.php'"><i class="fas fa-arrow-left"></i> Retour vers la liste des baux</button>
        </div>
        <div class="bail-details" id="bail-details">
            <p><i class="fas fa-check-circle"></i> Le bail a été créé avec succès. Voici les détails :</p>
            <br>
            <h3><i class="fas fa-user"></i> Informations Locataire:</h3>
            <p><strong>Nom et Prenom:</strong> <?= htmlspecialchars($locataire['nom']) ?> <?= htmlspecialchars($locataire['prenom']) ?></p>
            <p><strong>Adresse: </strong><?= htmlspecialchars($locataire['addresse']) ?>, <?= htmlspecialchars($locataire['CP']) ?>, <?= htmlspecialchars($locataire['ville']) ?></p>
            <p><strong>Numéro de téléphone:</strong> <?= htmlspecialchars($locataire['telephone']) ?></p>
            <p><strong>Adresse électronique:</strong> <?= htmlspecialchars($locataire['email']) ?></p>
            <br><br>
            
            <h3><i class="fas fa-warehouse"></i> Informations garage:</h3>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($garage['addresse']) ?> - <?= htmlspecialchars($garage['CP']) ?>-<?= htmlspecialchars($garage['ville']) ?>-<?= htmlspecialchars($garage['pays']) ?></p>
            <p><strong>Numéro:</strong> <?= htmlspecialchars($garage['numero_garage']) ?></p>
            <p><strong>Lot:</strong><?= htmlspecialchars($garage['numero_lot']) ?></p>
            <p><strong>Surface:</strong><?= htmlspecialchars($garage['surface']) ?></p>
            <br><br>
            <h3><i class="fas fa-file-contract"></i> Informations bail:</h3>
            <p><strong>Fait le:</strong> <?= htmlspecialchars($bail['fait_le']) ?></p>
            <p><strong>Fait à:</strong> <?= htmlspecialchars($bail['fait_a']) ?></p>
            <p><strong>Date de Début:</strong> <?= htmlspecialchars($bail['date_debut']) ?></p>
            <p><strong>Durée:</strong> <?= htmlspecialchars($bail['duree']) ?></p>
            <p><strong>Nombre de Clés:</strong> <?= htmlspecialchars($bail['nombre_de_clefs']) ?></p>
            <p><strong>Nombre de Bips:</strong> <?= htmlspecialchars($bail['nombre_de_bips']) ?></p>
            <p><strong>Montant du Loyer:</strong> <?= htmlspecialchars($bail['montant_loyer']) ?></p>
            <p><strong>Montant des Charges:</strong> <?= htmlspecialchars($bail['montant_charges']) ?></p>
            <p><strong>Total Mensuel:</strong> <?= htmlspecialchars($bail['total_mensuel']) ?></p>
            <p><strong>Prorata/Jour:</strong> <?= htmlspecialchars($bail['prorata']) ?></p>
            <p><strong>Caution:</strong> <?= htmlspecialchars($bail['caution']) ?></p>
        </div>
        
        <?php if (isset($email_sent)): ?>
            <div class="email-status">
                <?php if ($email_sent): ?>
                    <p class="success-message"><i class="fas fa-envelope"></i> Email envoyé avec succès au locataire.</p>
                <?php else: ?>
                    <p class="error-message"><i class="fas fa-exclamation-triangle"></i> Erreur lors de l'envoi de l'email. Veuillez réessayer.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>


<script>
    let isEditMode = false;
    const originalContent = document.getElementById('bail-details').innerHTML;

    function toggleEditMode() {
        const bailDetails = document.getElementById('bail-details');
        const editButton = document.getElementById('edit-button');

        if (isEditMode) {
            // Revenir au contenu original si on est en mode d'édition
            bailDetails.innerHTML = originalContent;
            editButton.innerHTML = '<i class="fas fa-edit"></i> Modifier les informations du bail';
            isEditMode = false;
        } else {
            // Entrer en mode d'édition
            bailDetails.innerHTML = `
                <form action="update_bail.php" method="post">
                    <input type="hidden" name="bail_id" value="<?= $bail_id ?>">

                    <div class="flex-container">
                        <div>
                            <label for="fait_le"><i class="fas fa-calendar-day"></i> Fait le:</label>
                            <input type="date" name="fait_le" value="<?= htmlspecialchars($bail['fait_le']) ?>" required>
                        </div>
                        <div>
                            <label for="fait_a"><i class="fas fa-map-marker-alt"></i> Fait à:</label>
                            <input type="text" name="fait_a" value="<?= htmlspecialchars($bail['fait_a']) ?>" required>
                        </div>
                        <div>
                            <label for="date_debut"><i class="fas fa-calendar-alt"></i> Date de Début:</label>
                            <input type="date" name="date_debut" value="<?= htmlspecialchars($bail['date_debut']) ?>" required>
                        </div>
                    </div>

                    <div class="flex-container">
                        <div>
                            <label for="nombre_de_clefs"><i class="fas fa-key"></i> Nombre de clefs:</label>
                            <input type="number" name="nombre_de_clefs" value="<?= htmlspecialchars($bail['nombre_de_clefs']) ?>" required>
                        </div>
                        <div>
                            <label for="nombre_de_bips"><i class="fas fa-remote"></i> Nombre de Bips:</label>
                            <input type="number" name="nombre_de_bips" value="<?= htmlspecialchars($bail['nombre_de_bips']) ?>" required>
                        </div>
                    </div>

                    <div class="flex-container">
                        <div>
                            <label for="montant_loyer"><i class="fas fa-money-bill-wave"></i> Montant du Loyer:</label>
                            <input type="text" name="montant_loyer" value="<?= htmlspecialchars($bail['montant_loyer']) ?>" required>
                        </div>
                        <div>
                            <label for="montant_charges"><i class="fas fa-coins"></i> Montant des Charges:</label>
                            <input type="text" name="montant_charges" value="<?= htmlspecialchars($bail['montant_charges']) ?>" required>
                        </div>
                    </div>

                    <div>
                        <label for="total_mensuel"><i class="fas fa-calculator"></i> Total Mensuel:</label>
                        <input type="text" name="total_mensuel" value="<?= htmlspecialchars($bail['total_mensuel']) ?>" readonly>
                    </div>

                    <div>
                        <label for="prorata"><i class="fas fa-percentage"></i> Loyer du premier mois:</label>
                        <input type="text" name="prorata" value="<?= htmlspecialchars($bail['prorata']) ?>" readonly>
                    </div>

                    <div>
                        <label for="caution"><i class="fas fa-piggy-bank"></i> Caution:</label>
                        <input type="text" name="caution" value="<?= htmlspecialchars($bail['caution']) ?>" required>
                    </div>

                    <div class="flex-container">
                        <input type="submit" value="Enregistrer les modifications">
                        <button type="button" onclick="toggleEditMode()"><i class="fas fa-times"></i> Annuler</button>
                    </div>
                </form>
            `;
            editButton.innerHTML = '<i class="fas fa-times"></i> Annuler';
            isEditMode = true;
        }
    }
</script>

</body>
</html>
