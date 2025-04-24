<?php
session_start();
include_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php");
    exit();
}

// Récupérer le contenu actuel et l'objet de l'email de relance depuis la base de données
$template_name = 'bail_pdf';
$stmt = $db->prepare("SELECT subject, content FROM email_templates WHERE template_name = ?");
$stmt->execute([$template_name]);
$email_template = $stmt->fetch(PDO::FETCH_ASSOC);
$subject = $email_template['subject'];
$content = $email_template['content'];

// Définir un contenu par défaut si l'entrée n'existe pas
if (!$content) {
    $content = "<p>Votre contenu par défaut ici</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Email d'envoi du bail</title>

    <!-- include summernote css/js -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
	
    <style>
        #emailContent {
            width: 50%;
        }
    </style>
</head>
<body>
    <h1>Modifier l'email d'envoi du bail</h1>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <form method="post" action="save_email.php">
    <input type="hidden" name="template_name" value="bail_pdf">
    <label for="emailSubject">Objet de l'email :</label><br>
    <input type="text" id="emailSubject" name="emailSubject" value="<?php echo htmlspecialchars($subject); ?>"><br><br>
    <label for="emailContent">Contenu de l'email :</label><br>
    <textarea id="emailContent" name="emailContent" rows="15" cols="80"><?php echo htmlspecialchars($content); ?></textarea>
    <br>
    <button type="submit">Enregistrer</button>
</form>


    <!-- Modal -->

    <script>
        $(document).ready(function() {
            $('#emailContent').summernote({
                height: 200,
                minHeight: 200,
                maxHeight: 300,
                toolbar: [
					['style', ['style']],
          			['font', ['bold', 'underline', 'clear', 'italic']],
					['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            }).parent().css('width', '50%');
        });
    </script>
</body>
</html>
