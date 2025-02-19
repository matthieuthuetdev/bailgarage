<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérification de la connexion en tant que propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: connexion_proprietaire.php");
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_email') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $user_id = $_SESSION['user_id'];

    // Récupération de l'ID du propriétaire
    $stmt = $db->prepare("SELECT proprietaire_id FROM proprietaires WHERE user_id = ?");
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $proprietaire_id = $stmt->fetchColumn();
    $stmt->closeCursor();

    if (!$proprietaire_id) {
        echo "Erreur: L'ID du propriétaire n'a pas été trouvé pour cet utilisateur.";
        exit();
    }

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
            header("Location: ajout_locataire_mail.php?email=" . urlencode($email));
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
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<style>
        /* Reset and Global Styles */
        body, h1, h2, p, form, input, button, div, a, ul, li {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            color: #213c4a; /* Dark Gray/Black */
            box-sizing: border-box;
        }

        body {
    background-color: #e9eef1; /* Light Teal background */
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0; /* Supprime les marges par défaut du body */
}


        /* Container Styles */
        .container {
            background: #ffffff; /* Off-White */
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            color: #002011; /* Dark Teal */
            margin-bottom: 20px;
        }

        h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #213c4a; /* Dark Gray/Black */
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

        /* Message Styles */
        .error-message {
            color: #e74c3c; /* Red */
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .success-message {
            color: #2ecc71; /* Green */
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Form Styles */
        form fieldset {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        form legend {
            padding: 0 10px;
            font-weight: bold;
            color: #002011; /* Dark Teal */
        }

        form label {
            display: block;
            margin-bottom: 8px;
            color: #213c4a; /* Dark Gray/Black */
        }

        form input[type="email"],
        form input[type="text"],
        form textarea {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #213c4a; /* Dark Gray/Black */
            color: #ffffff; /* White */
            font-size: 14px;
            cursor: pointer;
        }

        button i {
            margin-right: 8px;
        }

        button:hover {
            background-color: #002011; /* Dark Teal */
        }

        a {
            color: #587f96; /* Light Teal */
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
            color: #213c4a; /* Dark Gray/Black */
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


        /* Responsive Styles */
        @media (max-width: 600px) {
            nav ul {
                flex-direction: column;
            }

            .container {
                padding: 15px;
            }

            form fieldset {
                padding: 10px;
            }
        }
    </style>


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
        <a href="locataires.php"><i class="fas fa-arrow-left"></i> Retour à la liste des locataires</a> <br><br>

        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <?= nl2br(htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            <?= nl2br(htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <h1> Options disponibles :</h1>

        <form id="emailForm" onsubmit="openPopup(); return false;">
            <input type="hidden" name="action" value="send_email">
            <fieldset>
                <legend><i class="fas fa-envelope"></i> Envoyer un Email avec le Lien du Formulaire</legend>
                <label for="email"><i class="fas fa-at"></i> Adresse Email du Locataire :</label>
                <input type="email" id="email" name="email" required><br><br>
                <button type="submit" name="send"><i class="fas fa-paper-plane"></i> Envoyer Email</button>
            </fieldset>
        </form>

        <!-- Modal -->
        <div id="emailModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2><i class="fas fa-edit"></i> Modifier le contenu de l'email</h2>
                <form id="modalEmailForm" method="post" action="ajout_locataire_mail_send.php">
                    <input type="hidden" id="modalEmail" name="email">
                    <label for="modalEmailSubject"> Objet de l'email :</label>
                    <input type="text" id="modalEmailSubject" name="emailSubject" style="width: 100%;"><br><br>
                    <textarea id="modalEmailContent" name="emailContent" rows="15" style="width: 100%;"></textarea><br>
                    <label for="sendCopy"><i class="fas fa-copy"></i> Envoyer une copie à moi-même :</label>
                    <input type="checkbox" id="sendCopy" name="sendCopy"><br><br>
                    <button type="button" onclick="sendEmail()"><i class="fas fa-paper-plane"></i> Envoyer</button>
                </form>
            </div>
        </div>

        <h2><i class="fas fa-exchange-alt"></i> Ou</h2>

        <p>Vous pouvez remplir le formulaire vous-même en utilisant le lien suivant :</p>
        <a href="form_locataire.php"><i class="fas fa-external-link-alt"></i> Accéder au Formulaire de Locataire</a>
    </div>

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
                data: { email: email },
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
                            data: { template_name: 'formulaire_locataire' },
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