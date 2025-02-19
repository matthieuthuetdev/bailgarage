<?php
session_start();
include_once '../includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/phpmailer/src/Exception.php';
require '../includes/phpmailer/src/PHPMailer.php';
require '../includes/phpmailer/src/SMTP.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['proprietaire'])) {
    header("Location: ajout_proprietaire.php");
    exit();
}

$proprietaire = $_SESSION['proprietaire'];

if (isset($_POST['send-email'])) {
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lililizalopi2021@gmail.com';
        $mail->Password = 'esbb phvk adqu zzgm';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        // Sender and recipient
        $mail->setFrom('lililizalopi2021@gmail.com', 'Admin BailGarage');
        $mail->addAddress($proprietaire['email']);
        
        // Récupérer le modèle d'email depuis la base de données
        $template_name = 'proprietaire_email';
        $stmt = $db->prepare("SELECT subject, content FROM email_templates WHERE template_name = ?");
        $stmt->execute([$template_name]);
        $email_template = $stmt->fetch(PDO::FETCH_ASSOC);
        $subject = $email_template['subject'];
        $content = $email_template['content'];
        
        // Remplacer les placeholders dans le contenu de l'email
        $content = str_replace(['{prenom}', '{nom}', '{email}', '{mot_de_passe}'], [$proprietaire['prenom'], $proprietaire['nom'], $proprietaire['email'], $proprietaire['mot_de_passe']], $content);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;
        
        // Send email
        $mail->send();
        echo "L'email a été envoyé avec succès.";
    } catch (Exception $e) {
        echo "L'email n'a pas pu être envoyé. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Synthèse du Propriétaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
    <div class="container">
        <h2>Synthèse du Propriétaire</h2>
        <p>Nom : <?= htmlspecialchars($proprietaire['nom']) ?></p>
        <p>Prénom : <?= htmlspecialchars($proprietaire['prenom']) ?></p>
        <p>Email : <?= htmlspecialchars($proprietaire['email']) ?></p>
        <p>Téléphone : <?= htmlspecialchars($proprietaire['telephone']) ?></p>
        <p>Société : <?= htmlspecialchars($proprietaire['societe']) ?></p>
        <p>Adresse : <?= htmlspecialchars($proprietaire['addresse']) ?></p>
        <p>Complément : <?= htmlspecialchars($proprietaire['complement']) ?></p>
        <p>Code Postal : <?= htmlspecialchars($proprietaire['CP']) ?></p>
        <p>Ville : <?= htmlspecialchars($proprietaire['ville']) ?></p>
        <p>Pays : <?= htmlspecialchars($proprietaire['pays']) ?></p>
        <p>IBAN : <?= htmlspecialchars($proprietaire['iban']) ?></p>
        <p>BIC : <?= htmlspecialchars($proprietaire['bic']) ?></p>
        <p>Mot de passe généré : <?= htmlspecialchars($proprietaire['mot_de_passe']) ?></p>

        <!-- Lien vers la pièce jointe -->
        <p>
            <a href="../proprietaire/piece_jointe_proprietaire.php?id=<?= $proprietaire['id'] ?>" target="_blank">Voir la pièce jointe</a>
        </p>

        <form action="synthese_proprietaire.php" method="POST">
            <button type="submit" name="send-email">Envoyer les identifiants par mail</button>
        </form>
		
        <button type="button" class="icon-button" onclick="window.open('ajout_proprietaire_mail.php', '_blank', 'width=800,height=600')" title="Cliquez ici pour modifier l'email automatique">
            <i class="fas fa-cog"></i> <!-- Icône de paramètres -->
            <span class="tooltip">Modifier l'email automatique</span>
        </button>

        <form action="ajout_proprietaire.php" method="GET" style="display: inline;">
            <button type="submit">Retour</button>
        </form>
    </div>
</body>
</html>
