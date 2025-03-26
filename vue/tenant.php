<h1>Liste des locataires</h1>
<?php
$message = $_SESSION["message"];
$_SESSION["message"] = "";
$tenant = new Tenants();
$liste = $tenant->read($_SESSION["ownerId"]);
?>
<a href="index.php?pageController=tenant&action=create" class="btnAction">Créer un locataire</a>
<?php echo $message;
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
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phoneNumber']) . "</td>";
        echo "<td><a href='index.php?pageController=tenant&action=display&id=" . $row["id"] . "'>Plus d'info</a></td>";
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