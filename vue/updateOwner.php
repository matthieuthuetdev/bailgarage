<?php
if (!empty($_POST)) {
    $owner = new Owners();
    $success = $owner->update(
        $_GET['id'],
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
}
$owner = new Owners();
$ownerInfo = $owner->read($_GET["id"]);
var_dump($ownerInfo);

?>
<h1>Modifier le propriétaire</h1>
<form action="" method="post">
    <div>
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" class="name" required value="<?php echo $ownerInfo['name']; ?>">
    </div>

    <div>
        <label for="firstName">Prénom :</label>
        <input type="text" name="firstName" id="firstName" class="firstName" required value="<?php echo $ownerInfo['firstName']; ?>">
    </div>

    <div>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" class="email" required value="<?php echo $ownerInfo['email']; ?>">
    </div>

    <div>
        <label for="company">Entreprise :</label>
        <input type="text" name="company" id="company" class="company" value="<?php echo $ownerInfo['company']; ?>">
    </div>

    <div>
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" class="address" required value="<?php echo $ownerInfo['address']; ?>">
    </div>

    <div>
        <label for="additionalAddress">Complément d'adresse :</label>
        <input type="text" name="additionalAddress" id="additionalAddress" class="additionalAddress" value="<?php echo $ownerInfo['name']; ?>">
    </div>

    <div>
        <label for="phoneNumber">Numéro de téléphone :</label>
        <input type="tel" name="phoneNumber" id="phoneNumber" class="phoneNumber" required value="<?php echo $ownerInfo['additionalAddress'];?>">
    </div>

    <div>
        <label for="iban">IBAN :</label>
        <input type="text" name="iban" id="iban" class="iban" required value="<?php echo $ownerInfo['phoneNumber'];?>">
    </div>

    <div>
        <label for="bic">BIC :</label>
        <input type="text" name="bic" id="bic" class="bic" required value="<?php echo $ownerInfo['bic'];?>">
    </div>

    <div>
        <label for="attachmentPath">Pièce jointe :</label>
        <input type="text" name="attachmentPath" id="attachmentPath" class="attachmentPath" value="<?php echo $ownerInfo['attachmentPath'];?>">
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