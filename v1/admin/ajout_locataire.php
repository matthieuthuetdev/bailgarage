<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_email') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    // Vérification de l'email
    if (!$email) {
        $_SESSION['message'] = "Aucun email n'a été entré.";
        $_SESSION['email_sent'] = false;
    } else {
        // Vérifier si l'email existe déjà pour ce propriétaire
        $stmt_check_email = $db->prepare("SELECT COUNT(*) FROM locataires WHERE email = ? AND proprietaire_id = ?");
        $stmt_check_email->execute([$email, $proprietaire_id]);
        $count_email = $stmt_check_email->fetchColumn();
        $stmt_check_email->closeCursor();

        if ($count_email > 0) {
            // L'email existe déjà pour ce propriétaire
            $_SESSION['message'] = "L'adresse email existe déjà pour ce propriétaire.";
            $_SESSION['email_sent'] = false;
        } else {
            header("Location: ajout_locataire_mail.php?email=" . urlencode($email) . "&proprietaire_id=" . $proprietaire_id);
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix pour le Locataire</title>
    <link rel="stylesheet" href="../css/style4.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        /* General styles for the body */
        body {
            font-family: Arial, sans-serif;
            background-color: #e9eef1; /* Light grayish blue */
            color: #213c4a; /* Dark gray/black */
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Styles pour le menu déroulant */
        nav ul {
            list-style-type: none;
            padding: 0;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            background-color: #213c4a; /* Dark Gray/Black */
            border-radius: 4px;
            position: relative; /* Ajouté pour le positionnement du sous-menu */
        }

        nav ul li {
            position: relative; /* Ajouté pour le positionnement du sous-menu */
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
            display: none; /* Masquer le sous-menu par défaut */
            position: absolute; /* Positionner le sous-menu */
            top: 100%; /* Placer le sous-menu juste en dessous du parent */
            left: 0;
            background-color: #213c4a; /* Dark Gray/Black */
            border-radius: 4px;
            list-style-type: none;
            padding: 0;
            margin: 0;
            z-index: 1000; /* S'assurer que le sous-menu est au-dessus des autres éléments */
        }

        nav ul li:hover ul {
            display: block; /* Afficher le sous-menu lorsque le parent est survolé */
        }

        nav ul li ul li {
            width: 100%; /* S'assurer que les éléments du sous-menu prennent toute la largeur */
        }

        nav ul li ul li a {
            padding: 10px 20px; /* Ajuster le padding pour les éléments du sous-menu */
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

        /* Container for the form */
        form {
            background-color: #ffffff; /* White */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
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

        /* Autres styles */
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
    </style>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</head>
<body>
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
    <a href="locataires.php?proprietaire_id=<?= $proprietaire_id ?>">Retour à la liste des locataires</a>
    <h1>Choix pour le Locataire</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?= nl2br(htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
            <?= nl2br(htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <h2>Options disponibles :</h2>

    <form id="emailForm" onsubmit="openPopup(); return false;">
        <input type="hidden" name="action" value="send_email">
        <input type="hidden" name="proprietaire_id" value="<?= $proprietaire_id ?>">
        <fieldset>
            <legend>Envoyer un Email avec le Lien du Formulaire</legend>
            <label for="email">Adresse Email du Locataire :</label>
            <input type="email" id="email" name="email" required><br><br>
            <button type="submit" name="send">Envoyer Email</button>
        </fieldset>
    </form>

    <!-- Modal -->
    <div id="emailModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier le contenu de l'email</h2>
            <form id="modalEmailForm" method="post" action="ajout_locataire_mail_send.php">
                <input type="hidden" id="modalEmail" name="email">
                <input type="hidden" name="proprietaire_id" value="<?= $proprietaire_id ?>">
                <label for="modalEmailSubject">Objet de l'email :</label>
                <input type="text" id="modalEmailSubject" name="emailSubject" style="width: 100%;"><br><br>
                <textarea id="modalEmailContent" name="emailContent" rows="15" style="width: 100%;"></textarea><br>
                <label for="sendCopy">Envoyer une copie à moi-même :</label>
                <input type="checkbox" id="sendCopy" name="sendCopy"><br><br>
                <button type="button" onclick="sendEmail()">Envoyer</button>
            </form>
        </div>
    </div>

    <br>

    <h2>Ou</h2>

    <p>Vous pouvez remplir le formulaire vous-même en utilisant le lien suivant :</p>
    <a href="form_locataire.php" target="_blank">Accéder au Formulaire de Locataire</a>

    <script>
        // Fonction pour ouvrir le modal et initialiser Summernote
        function openModal() {
            document.getElementById('emailModal').style.display = 'block';

            // Initialiser Summernote après que le modal soit visible
            $('#modalEmailContent').summernote({
                height: 300,
                minHeight: 300,
                maxHeight: 500,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear', 'italic']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        // Fonction pour fermer le modal
        function closeModal() {
            // Détruire Summernote avant de fermer le modal
            $('#modalEmailContent').summernote('destroy');
            document.getElementById('emailModal').style.display = 'none';
        }

        // Récupérer le bouton de fermeture et ajouter un gestionnaire d'événements
        var closeBtn = document.getElementsByClassName('close')[0];
        closeBtn.onclick = closeModal;

        // Fermer le modal si l'utilisateur clique en dehors de celui-ci
        window.onclick = function(event) {
            var modal = document.getElementById('emailModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Fonction pour vérifier si l'email existe déjà dans la base de données
        function checkEmailExists(email) {
            return $.ajax({
                url: 'verifier_email.php', // Créez un fichier PHP pour vérifier l'email
                type: 'POST',
                data: { email: email, proprietaire_id: '<?= $proprietaire_id ?>' },
                dataType: 'json'
            });
        }

        // Fonction pour ouvrir le modal après vérification de l'email
        function openPopup() {
            var email = document.getElementById('email').value;
            if (email) {
                checkEmailExists(email).done(function(data) {
                    if (data.exists) {
                        alert('L\'adresse email existe déjà pour ce propriétaire.');
                    } else {
                        // Pré-remplir le champ email caché du modal
                        document.getElementById('modalEmail').value = email;

                        // Utiliser AJAX pour obtenir le modèle d'email
                        $.ajax({
                            url: 'ajout_locataire_mail.php',
                            type: 'GET',
                            data: { template_name: 'formulaire_locataire', proprietaire_id: '<?= $proprietaire_id ?>' },
                            dataType: 'json',
                            success: function(data) {
                                // Remplir les champs de sujet et de contenu avec les données récupérées
                                document.getElementById('modalEmailSubject').value = data.subject;
                                $('#modalEmailContent').summernote('code', data.content);

                                // Ouvrir le modal
                                openModal();
                            },
                            error: function(xhr, status, error) {
                                console.error('Erreur lors de la récupération du modèle d\'email:', error);
                                alert('Erreur lors de la récupération du modèle d\'email.');
                            }
                        });
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Erreur lors de la vérification de l\'email:', error);
                    alert('Erreur lors de la vérification de l\'email.');
                });
            } else {
                alert('Veuillez entrer une adresse email valide.');
            }
        }

        // Fonction pour envoyer l'email depuis le modal
        function sendEmail() {
            // Remplir les champs cachés avec les données du formulaire
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

            // Ajouter la case à cocher
            var sendCopy = document.getElementById('sendCopy').checked;
            $('<input>').attr({
                type: 'hidden',
                name: 'sendCopy',
                value: sendCopy
            }).appendTo(emailForm);

            // Soumettre le formulaire du modal
            emailForm.submit();
        }
    </script>
</body>
</html>

