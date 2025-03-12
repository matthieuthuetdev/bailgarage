<h1>Liste des propriétaires</h1>
<?php
$message = $_SESSION["message"];
$_SESSION["message"] = "";
$owner = new Owners();
$liste = $owner->read();
var_dump($liste);
?>
<?php echo $message ?>
<div>
    <a href="index.php?pageController=owner&action=create">Créé un propriétaire</a>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Numéro de téléphone</th>
                <th>Email</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            foreach ($liste as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phoneNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td><a href='index.php?pageController=owner&action=update&id=" .$row["id"]."'>Modifier</a></td>";
                echo "<td><a href='index.php?pageController=owner&action=delete&id=" .$row["id"]."'>Supprimer</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>
