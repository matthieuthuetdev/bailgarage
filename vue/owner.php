<h1>Liste des propriétaires</h1>
<?php
$message = $_SESSION["message"];
$_SESSION["message"] = "";
$owner = new Owners();
$liste = $owner->read();
?>
<a href="index.php?pageController=owner&action=create" class="btnAction">Créé un propriétaire</a>
<?php echo $message;
if (empty($_GET["id"])) {
    echo "<div>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nom</th>";
    echo "<th>Prénom</th>";
    echo "<th>Numéro de téléphone</th>";
    echo "<th>Email</th>";
    echo "<th>Aider ce propriétaire</th>";
    echo "<th>plus d'info</th>";
    echo "<th>Modifier</th>";
    echo "<th>Supprimer</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";


    foreach ($liste as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phoneNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td><a href='index.php?pageController=owner&action=help&id=" . $row["ownerId"] . "'>Aider ce propriétaire</a></td>";
        echo "<td><a href='index.php?pageController=owner&action=display&id=" . $row["ownerId"] . "'>plus d'info</a></td>";
        echo "<td><a href='index.php?pageController=owner&action=update&id=" . $row["ownerId"] . "'>Modifier</a></td>";
        echo "<td><a href='index.php?pageController=owner&action=delete&id=" . $row["ownerId"] . "'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    $ownerInfo = $owner->read($_GET["id"]);

    echo "<h2>information sur le propriétaire sélectionné :</h2>";
    echo "Nom : " . $ownerInfo["name"] . "<br>";
    echo "Prénom : " . $ownerInfo["firstName"] . "<br>";
    echo "Email : " . $ownerInfo["email"] . "<br>";
    echo "Numéro de téléphone : " . $ownerInfo["phoneNumber"] . "<br>";
    echo "Nom de l'entreprise : " . $ownerInfo["company"] . "<br>";
    echo "Adresse : " . $ownerInfo["address"] . "<br>";
    echo "complément d'adresse : " . $ownerInfo["additionalAddress"] . "<br>";
    echo "IBAN : " . $ownerInfo["iban"] . "<br>";
    echo "BIC : " . $ownerInfo["bic"] . "<br>";
    echo "Pièce jointe : " . $ownerInfo["attachmentId"] . "<br>";
    echo "genre : " . $ownerInfo["gender"] . "<br>";
}
?>