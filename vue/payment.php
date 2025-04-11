<h1>Liste des paiements</h1>
<?php
$message = $_SESSION["message"] ?? "";
$_SESSION["message"] = "";

$payment = new Payments();
$liste = $payment->read($_SESSION["ownerId"]);

echo "<a href='index.php?pageController=payment&action=create' class='btnAction'>Créer un paiement</a>";
echo $message;

if (empty($_GET["id"])) {
    echo "<div>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nom</th>";
    echo "<th>Prénom</th>";
    echo "<th>Garage</th>";
    echo "<th>Montant payé (€)</th>";
    echo "<th>Plus d'info</th>";
    echo "<th>Modifier</th>";
    echo "<th>Supprimer</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($liste as $row) {
        $lease = new Leases();
        $leaseInfo = $lease->read($_SESSION["ownerId"], $row["leaseId"]);

        $tenant = new Tenants();
        $tenantInfo = $tenant->read($_SESSION["ownerId"], $leaseInfo["tenantId"]);

        $garage = new Garages();
        $garageInfo = $garage->read($_SESSION["ownerId"], $leaseInfo["garageId"]);

        echo "<tr>";
        echo "<td>" . htmlspecialchars($tenantInfo["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($tenantInfo["firstName"]) . "</td>";
        echo "<td>" . htmlspecialchars($garageInfo["id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["amount"]) . "</td>";
        echo "<td><a href='index.php?pageController=payment&action=display&id=" . $row["id"] . "&leaseId=" . $row["leaseId"] . "'>Plus d'info</a></td>";
        echo "<td><a href='index.php?pageController=payment&action=update&id=" . $row["id"] . "&leaseId=" . $row["leaseId"] . "'>Modifier</a></td>";
        echo "<td><a href='index.php?pageController=payment&action=delete&id=" . $row["id"] . "&leaseId=" . $row["leaseId"] . "'>Supprimer</a></td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    $paymentInfo = $payment->read($_SESSION["ownerId"], $_GET["id"]);
    var_dump($paymentInfo);
    echo "<h2>Info du paiment sélectionné</h2>";
    echo "<a href='index.php?pageController=lease&action=display&id=" . $paymentInfo["leaseId"] . "' class='btnAction'>Voir le bail lier a ce paiment</a><br>";
    echo "Mois : " . htmlspecialchars($paymentInfo["monthPayment"]) . "<br>";
    echo "Montant : " . htmlspecialchars($paymentInfo["amount"]) . " €<br>";
    echo "Méthode de paiement : " . htmlspecialchars($paymentInfo["methodPayment"]) . "<br>";
    echo "Statut : " . ($paymentInfo["status"] ? "Payé" : "Non payé") . "<br>";
    echo "Note du propriétaire : " . (!empty($paymentInfo["ownerNote"]) ? htmlspecialchars($paymentInfo["ownerNote"]) : "Aucune") . "<br>";
}
?>