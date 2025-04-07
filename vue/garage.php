<h1>Liste des garages</h1>
<?php
$message = $_SESSION["message"];
$_SESSION["message"] = "";
$garage = new Garages();
$additionalIban = new additionalibans();
$liste = $garage->read($_SESSION["ownerId"]);
?>
<a href="index.php?pageController=garage&action=create" class="btnAction">Créer un garage</a>
<?php echo $message;
if (empty($_GET["id"])) {
    echo "<div>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Numéro du garage</th>";
    echo "<th>Adresse</th>";
    echo "<th>Complément d'adresse</th>";
    echo "<th>plus d'info</th>";
    echo "<th>Dupliquer</th>";
    echo "<th>Modifier</th>";
    echo "<th>Supprimer</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($liste as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['additionalAddress']) . "</td>";
        echo "<td><a href='index.php?pageController=garage&action=display&id=" . $row["id"] . "'>plus d'info</a></td>";
        echo "<td><a href='index.php?pageController=garage&action=duplicate&id=" . $row["id"] . "'>Dupliquer</a></td>";
        echo "<td><a href='index.php?pageController=garage&action=update&id=" . $row["id"] . "'>Modifier</a></td>";
        echo "<td><a href='index.php?pageController=garage&action=delete&id=" . $row["id"] . "'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    $garageInfo = $garage->read($_SESSION["ownerId"], $_GET["id"]);
    $additionalIbanInfo = $additionalIban->read($_SESSION["ownerId"], $garageInfo["additionalIbanId"]);
    var_dump($additionalIbanInfo);
    echo "<h2>Informations sur le garage sélectionné :</h2>";
    echo "Adresse : " . $garageInfo["address"] . "<br>";
    echo "Complément d'adresse : " . $garageInfo["additionalAddress"] . "<br>";
    echo "Pays : " . $garageInfo["country"] . "<br>";
    echo "Numéro de garage : " . $garageInfo["garageNumber"] . "<br>";
    echo "Numéro de lot : " . $garageInfo["lotNumber"] . "<br>";
    echo "Loyer hors charges (€) : " . $garageInfo["rentWithoutCharges"] . "<br>";
    echo "Charges (€) : " . $garageInfo["charges"] . "<br>";
    echo "Surface (m²) : " . $garageInfo["surface"] . "<br>";
    echo "Référence : " . $garageInfo["reference"] . "<br>";
    echo "Pièce jointe : " . $garageInfo["attachmentName"] . "<br>";
    echo "Syndic : " . $garageInfo["trustee"] . "<br>";
    echo "Caution (€) : " . $garageInfo["caution"] . "<br>";
    $additionalIbanInfo = empty($additionalIbanInfo["name"]) ? "Principal" : $additionalIbanInfo["name"];
    echo "IBAN à utiliser pour ce garage : " . $additionalIbanInfo . "<br>";
    echo "Commentaire : " . (!empty($garageInfo["comment"]) ? $garageInfo["comment"] : "Aucun") . "<br>";
    echo "Note du propriétaire : " . (!empty($garageInfo["ownerNote"]) ? $garageInfo["ownerNote"] : "Aucune") . "<br>";
}
?>