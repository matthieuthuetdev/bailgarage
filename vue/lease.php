<h1>Liste des baux</h1>
<?php
$message = $_SESSION["message"] ?? "";
$_SESSION["message"] = "";

$lease = new Leases();
$liste = $lease->read($_SESSION["ownerId"]);

$garage = new Garages();
$tenants = new Tenants();

echo "<a href='index.php?pageController=lease&action=create' class='btnAction'>Créer un bail</a>";
echo $message;

echo "<div>";
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>Nom et Prénom du locataire</th>";
echo "<th>Adresse et numéro du garage</th>";
echo "<th>Date de début</th>";
echo "<th>Date de fin</th>";
echo "<th>Statut</th>";
echo "<th>Plus d'info</th>";
echo "<th>Modifier</th>";
echo "<th>Supprimer</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

foreach ($liste as $row) {
    $infoGarage = $garage->read($_SESSION["ownerId"], $row["garageId"]);
    $infoTenants = $tenants->read($_SESSION["ownerId"], $row["tenantId"]);

    echo "<tr>";
    echo "<td>" . htmlspecialchars($infoTenants['firstName']) . " " . htmlspecialchars($infoTenants['name']) . "</td>";
    echo "<td>" . htmlspecialchars($infoGarage['address']) . " - " . htmlspecialchars($infoGarage['garageNumber']) . "</td>";
    echo "<td>" . htmlspecialchars($row['startDate']) . "</td>";
    echo "<td>" . (!empty($row['endDate']) ? htmlspecialchars($row['endDate']) : "En cours") . "</td>";
    echo "<td>" . ($row['status'] == 1 ? "Actif" : "Inactif") . "</td>";
    echo "<td><a href='index.php?pageController=lease&action=display&id=" . $row["id"] . "'>Plus d'info</a></td>";
    echo "<td><a href='index.php?pageController=lease&action=update&id=" . $row["id"] . "' class='btnAction'>Modifier</a></td>";
    echo "<td><a href='index.php?pageController=lease&action=delete&id=" . $row["id"] . "' class='btnAction'>Supprimer</a></td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";

if (!empty($_GET["id"])) {
    $leaseInfo = $lease->read($_SESSION["ownerId"], $_GET["id"]);
    echo "<h2>Informations sur le bail sélectionné :</h2>";
    echo "Date de début : " . htmlspecialchars($leaseInfo["startDate"]) . "<br>";
    echo "Date de fin : " . (!empty($leaseInfo["endDate"]) ? htmlspecialchars($leaseInfo["endDate"]) : "En cours") . "<br>";
    echo "Durée : " . htmlspecialchars($leaseInfo["duration"]) . " mois<br>";
    echo "Loyer hors charges (€) : " . htmlspecialchars($leaseInfo["rentAmount"]) . "<br>";
    echo "Charges (€) : " . htmlspecialchars($leaseInfo["chargesAmount"]) . "<br>";
    echo "Total mensuel (€) : " . htmlspecialchars($leaseInfo["totalAmountMonthly"]) . "<br>";
    echo "Prorata (€) : " . htmlspecialchars($leaseInfo["prorata"]) . "<br>";
    echo "Caution (€) : " . htmlspecialchars($leaseInfo["caution"]) . "<br>";
    echo "Nombre de clés : " . htmlspecialchars($leaseInfo["numberOfKey"]) . "<br>";
    echo "Nombre de bip : " . htmlspecialchars($leaseInfo["numberOfBeep"]) . "<br>";
    echo "Statut : " . ($leaseInfo["status"] == 1 ? "Actif" : "Inactif") . "<br>";
    echo "Note du propriétaire : " . (!empty($leaseInfo["ownerNote"]) ? htmlspecialchars($leaseInfo["ownerNote"]) : "Aucune") . "<br>";

    echo "<h3>Informations sur le locataire lié a ce bail : </h3>";
    $tenantInfo = $tenants->read($_SESSION["ownerId"], $leaseInfo["tenantId"]);
    echo "Nom : " . htmlspecialchars($tenantInfo["name"]) . "<br>";
    echo "Prénom : " . htmlspecialchars($tenantInfo["firstName"]) . "<br>";
    echo "Adresse : " . htmlspecialchars($tenantInfo["address"]) . "<br>";
    echo "Complément d'adresse : " . htmlspecialchars($tenantInfo["additionalAddress"]) . "<br>";
    echo "Téléphone : " . htmlspecialchars($tenantInfo["phoneNumber"]) . "<br>";
    echo "Téléphone fixe : " . htmlspecialchars($tenantInfo["landlinePhoneNumber"]) . "<br>";
    echo "Email : " . htmlspecialchars($tenantInfo["email"]) . "<br>";
    echo "RGPD : " . ($tenantInfo["rgpd"] ? "Accepté" : "Non accepté") . "<br>";
    echo "Genre : " . ($tenantInfo["gender"] ? "Femme" : "Homme") . "<br>";
    echo "Quittance : " . ($tenantInfo["receipt"] ? "Oui" : "Non") . "<br>";
    echo "Note du propriétaire : " . (!empty($tenantInfo["ownerNote"]) ? htmlspecialchars($tenantInfo["ownerNote"]) : "Aucune") . "<br>";

    $garageInfo = $garage->read($_SESSION["ownerId"], $leaseInfo["garageId"]);
    echo "<h3>Informations sur le garage lié a ce bail :</h3>";
    echo "Adresse : " . $garageInfo["address"] . "<br>";
    echo "Complément d'adresse : " . $garageInfo["additionalAddress"] . "<br>";
    echo "Pays : " . $garageInfo["country"] . "<br>";
    echo "Numéro de garage : " . $garageInfo["garageNumber"] . "<br>";
    echo "Numéro de lot : " . $garageInfo["lotNumber"] . "<br>";
    echo "Loyer hors charges (€) : " . $garageInfo["rentWithoutCharges"] . "<br>";
    echo "Charges (€) : " . $garageInfo["charges"] . "<br>";
    echo "Surface (m²) : " . $garageInfo["surface"] . "<br>";
    echo "Référence : " . $garageInfo["reference"] . "<br>";
    echo "Syndic : " . $garageInfo["trustee"] . "<br>";
    echo "Caution (€) : " . $garageInfo["caution"] . "<br>";
    echo "Commentaire : " . (!empty($garageInfo["comment"]) ? $garageInfo["comment"] : "Aucun") . "<br>";
    echo "Note du propriétaire : " . (!empty($garageInfo["ownerNote"]) ? $garageInfo["ownerNote"] : "Aucune") . "<br>";

    echo "<br>";
    echo "<a href='index.php?pageController=lease&action=update&id=" . $leaseInfo["id"] . "' class='btnAction'>Modifier</a> ";
    echo "<a href='index.php?pageController=lease&action=generate&id=" . $leaseInfo["id"] . "' class='btnAction'>Générer et envoyer</a>";
    echo "<a href='index.php?pageController=lease&action=delete&id=" . $leaseInfo["id"] . "' class='btnAction'>Supprimer</a>";
}
?>
