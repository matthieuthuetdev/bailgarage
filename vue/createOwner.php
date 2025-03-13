<?php
if (!empty($_POST)) {
    $owner = new Owners();
    $success = $owner->create(
        $_POST['name'],
        $_POST['firstName'],
        $_POST['email'],
        $_POST['company'],
        $_POST['address'],
        $_POST['additionalAddress'],
        $_POST['phoneNumber'],
        $_POST['iban'],
        $_POST['bic'],
        $_POST['attachmentPath'],
        $_POST['gender']
    );
    var_dump($success);
}
?>
<form action="" method="post">
    <div>
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" class="name" required>
    </div>

    <div>
        <label for="firstName">Prénom :</label>
        <input type="text" name="firstName" id="firstName" class="firstName" required>
    </div>

    <div>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" class="email" required>
    </div>

    <div>
        <label for="company">Entreprise :</label>
        <input type="text" name="company" id="company" class="company">
    </div>

    <div>
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" class="address" required>
    </div>

    <div>
        <label for="additionalAddress">Complément d'adresse :</label>
        <input type="text" name="additionalAddress" id="additionalAddress" class="additionalAddress">
    </div>

    <div>
        <label for="phoneNumber">Numéro de téléphone :</label>
        <input type="tel" name="phoneNumber" id="phoneNumber" class="phoneNumber" required>
    </div>

    <div>
        <label for="iban">IBAN :</label>
        <input type="text" name="iban" id="iban" class="iban" required>
    </div>

    <div>
        <label for="bic">BIC :</label>
        <input type="text" name="bic" id="bic" class="bic" required>
    </div>

    <div>
        <label for="attachmentPath">Pièce jointe :</label>
        <input type="text" name="attachmentPath" id="attachmentPath" class="attachmentPath">
    </div>

    <div>
        <label for="gender">Genre :</label>
        <select name="gender" id="gender" class="gender" required>
            <option value="homme">Homme</option>
            <option value="femme">Femme</option>
        </select>
    </div>

    <button type="submit">Envoyer</button>
</form>
