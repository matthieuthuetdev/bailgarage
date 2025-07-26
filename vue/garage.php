<link rel="stylesheet" href="./css/garage.css">
<main class="main-content">

    <!-- Barre de recherche seule en haut -->
    <div class="search-container">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" placeholder="Recherche..." class="search-input" />
    </div>

    <div class="title-section">
        <h2 class="sub-title">Liste des garages</h2>
        <a href="index.php?pageController=garage&action=create" class="btnAction">CRÉER UN GARAGE</a>
    </div>

    <?php
    $message = $_SESSION["message"];
    $_SESSION["message"] = "";
    $garage = new Garages();
    $additionalIban = new additionalibans();
    $liste = $garage->read($_SESSION["ownerId"]);
    echo $message;
    ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Adresse</th>
                    <th>Complément</th>
                    <th>Plus d’info</th>
                    <th>Dupliquer</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($liste as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['garageNumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['additionalAddress']) . "</td>";
                    echo "<td><a href='index.php?pageController=garage&action=display&id=" . $row["id"] . "' class='info-link' data-garage-id='" . $row["id"] . "'><i class='fa-solid fa-info'></i></a></td>";
                    echo "<td><a href='index.php?pageController=garage&action=duplicate&id=" . $row["id"] . "' class='duplicate-link'><i class='fa-solid fa-copy'></i></a></td>";
                    echo "<td><a href='index.php?pageController=garage&action=update&id=" . $row["id"] . "' class='edit-link'><i class='fa-solid fa-pen-to-square'></i></a></td>";
                    echo "<td><a href='index.php?pageController=garage&action=delete&id=" . $row["id"] . "' class='delete-link'><i class='fa-solid fa-trash'></i></a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    if (!empty($_GET["id"])) {
        $garageInfo = $garage->read($_SESSION["ownerId"], $_GET["id"]);
        $additionalIbanInfo = $additionalIban->read($_SESSION["ownerId"], $garageInfo["additionalIbanId"]);
        $additionalIbanDisplay = empty($additionalIbanInfo["name"]) ? "par défaut" : $additionalIbanInfo["name"];
        $lotNumber = !empty($garageInfo["lotNumber"]) ? $garageInfo["lotNumber"] : "aucun";
    ?>
        <div id="garageModal" class="modal" style="display: flex;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <div class="card">
                    <h2>Informations sur le garage sélectionné</h2>
                    <div class="info-grid">
                        <p><strong>Adresse :</strong> <?= $garageInfo["address"] ?></p>
                        <p><strong>Complément :</strong> <?= $garageInfo["additionalAddress"] ?></p>
                        <p><strong>Ville :</strong> <?= $garageInfo["cityName"] ?></p>
                        <p><strong>Code postal :</strong> <?= $garageInfo["postalCode"] ?></p>
                        <p><strong>Pays :</strong> <?= $garageInfo["country"] ?></p>
                        <p><strong>Numéro de garage :</strong> <?= $garageInfo["garageNumber"] ?></p>
                        <p><strong>Numéro de lot :</strong> <?= $lotNumber ?></p>
                        <p><strong>Loyer HC (€) :</strong> <?= $garageInfo["rentWithoutCharges"] ?></p>
                        <p><strong>Charges (€) :</strong> <?= $garageInfo["charges"] ?></p>
                        <p><strong>Surface (m²) :</strong> <?= $garageInfo["surface"] ?></p>
                        <p><strong>Référence :</strong> <?= $garageInfo["reference"] ?></p>
                        <p><strong>Syndic :</strong> <?= $garageInfo["trustee"] ?></p>
                        <p><strong>Caution (€) :</strong> <?= $garageInfo["caution"] ?></p>
                        <p><strong>IBAN :</strong> <?= $additionalIbanDisplay ?></p>
                        <p><strong>Commentaire :</strong> <?= !empty($garageInfo["comment"]) ? $garageInfo["comment"] : "Aucun" ?></p>
                        <p><strong>Note :</strong> <?= !empty($garageInfo["ownerNote"]) ? $garageInfo["ownerNote"] : "Aucune" ?></p>
                    </div>
                    <div>
                        <a href='index.php?pageController=garage&action=duplicate&id=<?= $garageInfo["id"] ?>' class='btnAction'>Dupliquer</a>
                        <a href='index.php?pageController=garage&action=update&id=<?= $garageInfo["id"] ?>' class='btnAction'>Modifier</a>
                        <a href='index.php?pageController=garage&action=delete&id=<?= $garageInfo["id"] ?>' class='btnAction'>Supprimer</a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

</main>

<script>
    const modal = document.getElementById('garageModal');
    const closeBtn = document.querySelector('.close-btn');

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', e => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('.info-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.href;
        });
    });
</script>