<?php
$message = "";

if (!empty($_POST)) {
    if (empty($_POST['additionalIbanName'])) {
        $message = "Le nom de l'IBAN supplémentaire est obligatoire.";
    } elseif (empty($_POST['additionalIban']) || !preg_match("/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/", $_POST['additionalIban'])) {
        $message = "IBAN supplémentaire invalide.";
    } elseif (empty($_POST['additionalBic']) || !preg_match("/^[A-Z0-9]{8,11}$/", $_POST['additionalBic'])) {
        $message = "BIC supplémentaire invalide.";
    } else {
        $additionalIban = new additionalIbans();
            $additionalIban->create($_GET["id"], $_POST["additionalIbanName"], $_POST["additionalIban"], $_POST["additionalBic"]);
        $message = "IBAN ajouté avec succès !";
    }
    echo $message;
}
?>

<form method="post" action="">
    <h3>Créer un IBAN supplémentaire</h3>
    <div>
        <label for="additionalIbanName">Nom de l'IBAN supplémentaire :</label>
        <input type="text" name="additionalIbanName" id="additionalIbanName"
            value="<?php echo isset($_POST['additionalIbanName']) ? htmlspecialchars($_POST['additionalIbanName']) : ''; ?>">
    </div>

    <div>
        <label for="additionalIban">IBAN supplémentaire :</label>
        <input type="text" name="additionalIban" id="additionalIban"
            value="<?php echo isset($_POST['additionalIban']) ? htmlspecialchars($_POST['additionalIban']) : ''; ?>">
    </div>

    <div>
        <label for="additionalBic">BIC supplémentaire :</label>
        <input type="text" name="additionalBic" id="additionalBic"
            value="<?php echo isset($_POST['additionalBic']) ? htmlspecialchars($_POST['additionalBic']) : ''; ?>">
    </div>

    <button type="submit">Envoyer</button>
</form>

<?php
?>