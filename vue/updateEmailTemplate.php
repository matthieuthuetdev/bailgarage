<?php
$emailTemplate = new EmailTemplate();

function displayValue($value) {
    return !empty($value) ? htmlspecialchars($value) : '';
}

if (!empty($_POST)) {
    $message = "";

    if (empty($_POST['subject'])) {
        $message = "Le sujet est obligatoire.";
    } elseif (empty($_POST['content'])) {
        $message = "Le contenu est obligatoire.";
    } else {
        $success = $emailTemplate->update(
            $_GET['name'],
            $_POST['subject'],
            $_POST['content']
        );
        $message = $success ? "Modèle d'email modifié avec succès." : "Erreur lors de la modification du modèle d'email.";
    }
    echo "<div>$message</div>";
}

$emailTemplateInfo = $emailTemplate->read($_GET['name']);

?>

<h1>Modifier un modèle d'email</h1>
<form action="" method="post">

    <div>
        <label for="subject">Sujet :</label>
        <input type="text" name="subject" id="subject" required value="<?php echo displayValue(isset($_POST['subject']) ? $_POST['subject'] : $emailTemplateInfo['subject']); ?>">
    </div>

    <div>
        <label for="content">Contenu :</label>
        <textarea name="content" id="content" rows="5" required><?php echo displayValue(isset($_POST['content']) ? $_POST['content'] : $emailTemplateInfo['content']); ?></textarea>
    </div>

    <button type="submit">Enregistrer</button>
</form>