<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation du Bail</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styles for the body */
        body {
            font-family: Arial, sans-serif;
            background-color: #e9eef1; /* Light grayish blue */
            color: #213c4a; /* Dark gray/black */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container styles */
        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 600px;
        }

        .container {
            background-color: #ffffff; /* White */
            padding: 20px;
            border-radius: 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        /* Nav styles */
        nav {
            width: 100%;
            margin-bottom: 20px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: space-around;
            background-color: #213c4a; /* Dark Gray/Black */
            border-radius: 4px;
        }

        nav ul li {
            position: relative;
        }

        nav ul li a {
            color: #ffffff; /* White */
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        nav ul li a:hover {
            background-color: #002011; /* Dark Teal */
        }

        nav ul li ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #213c4a;
            border-radius: 4px;
            list-style-type: none;
            padding: 0;
            margin: 0;
            z-index: 1000;
        }

        nav ul li:hover ul {
            display: block;
        }

        nav ul li ul li {
            width: 100%;
        }

        nav ul li ul li a {
            padding: 10px 20px;
        }

        /* Link back to the garage list */
        a {
            color: #213c4a; /* Dark gray/black */
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Heading styles */
        h2 {
            color: #213c4a; /* Dark gray/black */
        }

        /* General label and input styles */
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Button styles */
        button {
            background-color: #213c4a; /* Dark gray/black */
            color: #ffffff; /* White */
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #002011; /* Dark teal */
        }

        /* Style pour le modal */
        .modal {
            display: none; /* Masquer le modal par défaut */
            position: fixed; /* Positionner le modal de manière fixe */
            z-index: 1; /* Placer le modal au-dessus de tout */
            left: 0;
            top: 0;
            width: 100%; /* Largeur totale de la page */
            height: 100%; /* Hauteur totale de la page */
            overflow: auto; /* Permettre le défilement si nécessaire */
            background-color: rgb(0,0,0); /* Couleur de fond */
            background-color: rgba(0,0,0,0.4); /* Fond semi-transparent */
        }

        /* Style pour le contenu du modal */
        .modal-content {
            position: absolute;
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Largeur du contenu du modal */
            max-width: 600px; /* Largeur maximale du contenu du modal */
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%); /* Centrer horizontalement et verticalement */
        }

        /* Style pour le bouton de fermeture */
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

        .error-message {
            color: red;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .success-message {
            color: green;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .email-status {
            margin-bottom: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</head>
<body>
    <div class="wrapper">
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

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="email-status">
                <p class="success-message"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="email-status">
                <p class="error-message"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="container">
            <h2>Documents liés au bail</h2>
            <p>Cliquez ici pour accéder au PDF généré automatiquement à partir des informations de votre bail:</p>
            <button onclick="window.open('generate_pdf.php?id=<?= htmlspecialchars($bail_id) ?>', '_blank')" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Voir PDF du bail</button>

            <br><br>
            <p>Envoyer le bail en pièce jointe pour signature:</p>
            <button onclick="openModal()"><i class="fas fa-envelope"></i> Envoyer Email</button>

            <hr>
            <p>Retrouvez ici la version du bail signée par le locataire:</p>
            <?php if (!empty($bail['pdf_path'])): ?>
                <a href="<?= htmlspecialchars($bail['pdf_path']) ?>" target="_blank"><i class="fas fa-download"></i> Voir ou télécharger le bail signé</a>
            <?php else: ?>
                <p>Aucun bail signé n'a été téléchargé.</p>
            <?php endif; ?>

            <p>Ou</p>
            <p>Importez vous-même le bail signé sur votre espace en cliquant sur le lien suivant:</p>
            <a href="import.php?id=<?= htmlspecialchars($bail_id) ?>" target="_blank"><i class="fas fa-upload"></i> Importer le bail</a>
        </div>
    </div>

    <div id="emailModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier le contenu de l'email</h2>
            <form id="modalEmailForm" method="post" action="confirmation_mail_send.php">
                <input type="hidden" name="bail_id" value="<?= htmlspecialchars($bail_id) ?>">
                <input type="hidden" id="modalEmail" name="email" value="<?= htmlspecialchars($locataire['email']) ?>">
                <label for="modalEmailSubject">Objet de l'email :</label>
                <input type="text" id="modalEmailSubject" name="emailSubject" style="width: 100%;"><br><br>
                <textarea id="modalEmailContent" name="emailContent" rows="15" style="width: 100%;"></textarea><br>
                <label for="sendCopy">Envoyer une copie à moi-même :</label>
                <input type="checkbox" id="sendCopy" name="sendCopy"><br><br>
                <p><strong>Le PDF du bail sera envoyé en pièce jointe.</strong></p>
                <button type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var closeEmailBtn = document.querySelector('#emailModal .close');
            closeEmailBtn.onclick = closeModal;

            window.onclick = function(event) {
                var emailModal = document.getElementById('emailModal');
                if (event.target == emailModal) {
                    closeModal();
                }
            };
        });

        function openModal() {
            document.getElementById('emailModal').style.display = 'block';

            // Charger le modèle d'email depuis la base de données
            $.ajax({
                url: 'confirmation_mail.php',
                type: 'GET',
                data: { template_name: 'bail_pdf', bail_id: '<?= $bail_id; ?>' },
                dataType: 'json',
                success: function(data) {
                    document.getElementById('modalEmailSubject').value = data.subject;
                    $('#modalEmailContent').summernote({
                        height: 300,
                        minHeight: 300,
                        maxHeight: 500,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear', 'italic']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ]
                    }).summernote('code', data.content);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la récupération du modèle d\'email:', error);
                    alert('Erreur lors de la récupération du modèle d\'email.');
                }
            });
        }

        function closeModal() {
            $('#modalEmailContent').summernote('destroy');
            document.getElementById('emailModal').style.display = 'none';
        }

        function sendEmail() {
            var emailContent = $('#modalEmailContent').summernote('code');
            var emailSubject = document.getElementById('modalEmailSubject').value;
            var emailForm = document.getElementById('modalEmailForm');

            $('<input>').attr({
                type: 'hidden',
                name: 'emailContent',
                value: emailContent
            }).appendTo(emailForm);

            $('<input>').attr({
                type: 'hidden',
                name: 'emailSubject',
                value: emailSubject
            }).appendTo(emailForm);

            $('<input>').attr({
                type: 'hidden',
                name: 'action',
                value: 'send_email'
            }).appendTo(emailForm);

            var sendCopy = document.getElementById('sendCopy').checked;
            $('<input>').attr({
                type: 'hidden',
                name: 'sendCopy',
                value: sendCopy
            }).appendTo(emailForm);

            emailForm.submit();
        }
    </script>
</body>
</html>
