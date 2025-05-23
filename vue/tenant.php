<h1>Liste des locataires</h1>
<?php
$message = $_SESSION["message"];
$_SESSION["message"] = "";
$tenant = new Tenants();
$liste = $tenant->read($_SESSION["ownerId"]);
if (!empty($_POST["email"])) {
    $message = "";
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide";
    } elseif (!empty($tenant->searchTenantByEmail($_POST["email"]))) {
        $message = "l'email est déjà en base de donnée";
    } else {
        $message = "le locataire a été ajouter avec succès ! Un mail vient de lui être envoyé avec le lien vers le formulaire.";
        $tenant->emailCreate($_SESSION["ownerId"], $_POST["email"]);
        $mail = new MailService();
        $mail->send($_POST["email"], "Formulaire location", "<h1> Formulaire location</h1><div>Bonjour.</div><p>Vous trouverez cidessous le lien afin de remplir vos informations pour compléter votre profil de locataire :</p><div><a href='https://app.bailgarage.fr/index.php?pageController=tenant&action=tenantform&id=" . $tenant->searchTenantByEmail($_POST["email"])["id"] . "&ownerId=" . $_SESSION["ownerId"] . "&email=" . $_POST["email"] . "'>Accéder au formulaire</a></div><div>Après avoir rempli le formulaire le lien restera actif si vous souhaitez modifier des informations</div>");
        $message = "Email envoyer au locataire avec succès !";
    }
    echo $message;
}

?>
<h3>Envoyer le lien du formulaire par email au locataire</h3>
<form method="post" action="">
    <label for="email">Adresse email du locataire</label>
    <input type="email" id="name" name="email">
    <button type="submit">Envoyer</button>
</form>
<div>OU</div>
<a href="index.php?pageController=tenant&action=create" class="btnAction">Créer un locataire manuèlement</a>
<?php
if (empty($_GET["id"])) {
    echo "<div>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nom</th>";
    echo "<th>Prénom</th>";
    echo "<th>Email</th>";
    echo "<th>Téléphone</th>";
    echo "<th>Plus d'info</th>";
    echo "<th>Modifier</th>";
    echo "<th>Supprimer</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($liste as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars(!empty($row["name"]) ? $row['name'] : "En attente") . "</td>";
        echo "<td>" . htmlspecialchars(!empty($row["name"]) ? $row['firstName'] : "En attente") . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars(!empty($row["name"]) ? $row['phoneNumber'] : "En attente") . "</td>";
        echo empty($row["name"]) ? "<td>En attente</td>" : "<td><a href='index.php?pageController=tenant&action=display&id=" . $row["id"] . "'>Plus d'info</a></td>";
        echo "<td><a href='index.php?pageController=tenant&action=update&id=" . $row["id"] . "'>Modifier</a></td>";
        echo "<td><a href='index.php?pageController=tenant&action=delete&id=" . $row["id"] . "'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    $tenantInfo = $tenant->read($_SESSION["ownerId"], $_GET["id"]);
    echo "<h2>Informations sur le locataire sélectionné :</h2>";
    echo "Nom : " . htmlspecialchars($tenantInfo["name"]) . "<br>";
    echo "Prénom : " . htmlspecialchars($tenantInfo["firstName"]) . "<br>";
    echo "Entreprise  : " . htmlspecialchars($tenantInfo["company"]) . "<br>";
    echo "Adresse : " . htmlspecialchars($tenantInfo["address"]) . "<br>";
    echo "Complément d'adresse : " . htmlspecialchars($tenantInfo["additionalAddress"]) . "<br>";
    echo "Téléphone : " . htmlspecialchars($tenantInfo["phoneNumber"]) . "<br>";
    echo "Téléphone fixe : " . htmlspecialchars($tenantInfo["landlinePhoneNumber"]) . "<br>";
    echo "Email : " . htmlspecialchars($tenantInfo["email"]) . "<br>";
    echo "RGPD : " . ($tenantInfo["rgpd"] ? "Accepté" : "Non accepté") . "<br>";
    echo "Pièce jointe : " . htmlspecialchars($tenantInfo["attachmentPath"]) . "<br>";
    echo "Genre : " . ($tenantInfo["gender"] ? "Femme" : "Homme") . "<br>";
    echo "Quittance : " . ($tenantInfo["receipt"] ? "Oui" : "Non") . "<br>";
    echo "Note du propriétaire : " . (!empty($tenantInfo["ownerNote"]) ? htmlspecialchars($tenantInfo["ownerNote"]) : "Aucune") . "<br>";
}
?>