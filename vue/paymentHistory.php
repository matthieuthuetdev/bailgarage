<h1>Liste des historiques de paiements</h1>
<?php
$message = $_SESSION["message"] ?? "";
$_SESSION["message"] = "";

$paymentHistory = new PaymentHistories();
$listeHistories = $paymentHistory->read($_SESSION["ownerId"]);

echo "<a href='index.php?pageController=paymenthistory&action=create' class='btnAction'>Créer un historique de paiement</a>";
echo $message;

if (empty($_GET["id"])) {
    echo "<div>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID de la location</th>";
    echo "<th>Montant payé (€)</th>";
    echo "<th>Date de paiement</th>";
    echo "<th>Méthode de paiement</th>";
    echo "<th>Modifier</th>";
    echo "<th>Supprimer</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($listeHistories as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["leasesId"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["amount"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["paymentDate"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["methode"]) . "</td>";  // Added method column
        echo "<td><a href='index.php?pageController=paymenthistory&action=update&id=" . $row["id"] . "'>Modifier</a></td>";
        echo "<td><a href='index.php?pageController=paymenthistory&action=delete&id=" . $row["id"] . "'>Supprimer</a></td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    $paymentHistoryInfo = $paymentHistory->read($_SESSION["ownerId"], $_GET["id"]);
    var_dump($paymentHistoryInfo);
    echo "<h2>Info de l'historique de paiement sélectionné</h2>";
    echo "ID de la location : " . htmlspecialchars($paymentHistoryInfo["leasesId"]) . "<br>";
    echo "ID de paiement : " . htmlspecialchars($paymentHistoryInfo["paymentId"]) . "<br>";
    echo "Montant : " . htmlspecialchars($paymentHistoryInfo["amount"]) . " €<br>";
    echo "Date de paiement : " . htmlspecialchars($paymentHistoryInfo["paymentDate"]) . "<br>";
    echo "Méthode de paiement : " . htmlspecialchars($paymentHistoryInfo["methode"]) . "<br>";  // Added method info
}
?>
