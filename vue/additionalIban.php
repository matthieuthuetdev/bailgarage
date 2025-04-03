<a href="index.php?pageController=additionalIban&action=create&id=<?php echo $_GET['ownerId'];?>" class="btnAction">Créer un IBAN Supplémentaires</a>
<?php
$additionalIban = new additionalibans();
$liste = $additionalIban->read($_GET["ownerId"]);
if (!empty($liste)) {
    echo "<div>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nom de l'IBAN</th>";
    echo "<th>IBAN</th>";
    echo "<th>BIC</th>";
    echo "<th>Modifier</th>";
    echo "<th>Supprimer</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($liste as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['iban']) . "</td>";
        echo "<td>" . htmlspecialchars($row['bic']) . "</td>";
        echo "<td><a href='index.php?pageController=additionalIban&action=update&id=" . $row["id"] . "&ownerId=".$_GET["ownerId"]."'>Modifier</a></td>";
        echo "<td><a href='index.php?pageController=additionalIban&action=delete&id=" . $row["id"] . "&ownerId=".$_GET["ownerId"]."'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}
?>