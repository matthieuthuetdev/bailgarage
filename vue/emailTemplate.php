<?php
echo "<h1>Liste des modèles d\"e-mail</h1>";

$emailTemplate = new EmailTemplate();
if (empty($_GET["name"])) {
    $liste = $emailTemplate->read();
    echo "<div>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nom</th>";
    echo "<th>Objet</th>";
    echo "<th>Aperçu</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($liste as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["subject"]) . "</td>";
        echo "<td><a href='index.php?pageController=emailtemplate&action=display&name=" . htmlspecialchars($row["name"]) . "'>Voir l'aperçu</a></td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    $emailTemolateInfo = $emailTemplate->read($_GET["name"]);
    echo "objet : ". htmlspecialchars($emailTemolateInfo["subject"]) ."<br>";
    echo  "message : ". $emailTemolateInfo["content"];
}
