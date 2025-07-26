<link rel="stylesheet" href="./css/tenant.css">
<main class="main-content">
        <?php
        echo "<h1>Liste des locataires</h1>";

<<<<<<< Updated upstream
if (!empty($_POST["email"])) {
    $message = "";
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide";
    } elseif (!empty($tenant->searchTenantByEmail($_POST["email"]))) {
        $message = "l'email est déjà en base de donnée";
    } else {
        $message = "le locataire a été ajouter avec succès ! Un mail vient de lui être envoyé avec le lien vers le formulaire.";
        $tenant->emailCreate($_SESSION["ownerId"], $_POST["email"]);
        $mail = new MailService();
        $link = "https://app.bailgarage.fr/index.php?pageController=tenant&action=tenantform&id=" . $tenant->searchTenantByEmail($_POST["email"])["id"] . "&ownerId" . $_SESSION["ownerId"] . "&email=" . $_POST["email"];
        $mail->sendTemplate($_POST["email"], "tenantForm", array("link" => $link));
        $message = "Email envoyer au locataire avec succès !";
    }
    echo $message;
}
?>
=======
        $message = $_SESSION["message"];
        $_SESSION["message"] = "";
        $tenant = new Tenants();
        $liste = $tenant->read($_SESSION["ownerId"]);
>>>>>>> Stashed changes

        if (!empty($_POST["email"])) {
            $message = "";
            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $message = "Email invalide";
            } elseif (!empty($tenant->searchTenantByEmail($_POST["email"]))) {
                $message = "l'email est déjà en base de donnée";
            } else {
                $message = "le locataire a été ajouter avec succès ! Un mail vient de lui être envoyé avec le lien vers le formulaire.";
                $tenant->emailCreate($_SESSION["ownerId"], $_POST["email"]);
                $mail = new MailService();
                $link = "https://app.bailgarage.fr/index.php?pageController=tenant&action=tenantform&id=" . $tenant->searchTenantByEmail($_POST["email"])["id"] . "&ownerId=" . $_SESSION["ownerId"] . "&email=" . $_POST["email"];
                $mail->sendTemplate($_POST["email"], "tenantForm", array("link" => $link));
                $message = "Email envoyer au locataire avec succès !";
            }
            echo "<div class='message success'>{$message}</div>";
        }
        ?>

        <h3>Envoyer le lien du formulaire par email au locataire</h3>
        <div class="form-action-row">
            <form method="post" action="" class="inline-form">
                <input type="email" id="email" name="email" placeholder="Adresse email du locataire" required />
                <button type="submit" class="btnAction">ENVOYER</button>
            </form>
            <a href="index.php?pageController=tenant&action=create" class="btnAction add-btn">AJOUTER UN LOCATAIRE</a>
        </div>

        <?php
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
            echo "<td>" . htmlspecialchars(!empty($row["name"]) ? $row['name'] : "En attente") . "</td>";
            echo "<td>" . htmlspecialchars(!empty($row["name"]) ? $row['firstName'] : "En attente") . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars(!empty($row["name"]) ? $row['phoneNumber'] : "En attente") . "</td>";
            echo empty($row["name"]) ? "<td>En attente</td>" : "<td><a class='info-link' href='index.php?pageController=tenant&action=display&id=" . $row["id"] . "'><i class='fa-solid fa-info'></i></a></td>";
            echo "<td><a class='edit-link' href='index.php?pageController=tenant&action=update&id=" . $row["id"] . "'><i class='fa-solid fa-pen-to-square'></i></a></td>";
            echo "<td><a class='delete-link' href='index.php?pageController=tenant&action=delete&id=" . $row["id"] . "'><i class='fa-solid fa-trash'></i></a></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        ?>

        <?php
        if (!empty($_GET["id"])) {
            $tenantInfo = $tenant->read($_SESSION["ownerId"], $_GET["id"]);
            echo "<div id='tenantModal' class='modal' style='display:flex'>";
            echo "<div class='modal-content'>";
            echo "<span class='close-btn'>&times;</span>";
            echo "<div class='card'>";
            echo "<h2>Informations du locataire</h2>";
            echo "<div class='info-grid'>";
            echo "<p><strong>Nom :</strong> " . htmlspecialchars($tenantInfo["name"]) . "</p>";
            echo "<p><strong>Prénom :</strong> " . htmlspecialchars($tenantInfo["firstName"]) . "</p>";
            echo "<p><strong>Email :</strong> " . htmlspecialchars($tenantInfo["email"]) . "</p>";
            echo "<p><strong>Téléphone :</strong> " . htmlspecialchars($tenantInfo["phoneNumber"]) . "</p>";
            echo "<p><strong>Adresse :</strong> " . htmlspecialchars($tenantInfo["address"]) . "</p>";
            echo "<p><strong>Complément d'adresse :</strong> " . htmlspecialchars($tenantInfo["additionalAddress"]) . "</p>";
            echo "<p><strong>Ville :</strong> " . htmlspecialchars($tenantInfo["additionalAddress"]) . "</p>";
            echo "<p><strong>Code postal :</strong> " . htmlspecialchars($tenantInfo["additionalAddress"]) . "</p>";
            echo "<p><strong>Pays :</strong> " . htmlspecialchars($tenantInfo["country"]) . "</p>";
            echo "<p><strong>Fixe :</strong> " . htmlspecialchars($tenantInfo["landlinePhoneNumber"]) . "</p>";
            echo "<p><strong>RGPD :</strong> " . ($tenantInfo["rgpd"] ? "Accepté" : "Non accepté") . "</p>";
            echo "<p><strong>Genre :</strong> " . ($tenantInfo["gender"] ? "Femme" : "Homme") . "</p>";
            echo "<p><strong>Quittance :</strong> " . ($tenantInfo["receipt"] ? "Oui" : "Non") . "</p>";
            echo "<p><strong>Note :</strong> " . (!empty($tenantInfo["ownerNote"]) ? htmlspecialchars($tenantInfo["ownerNote"]) : "Aucune") . "</p>";
            echo "</div>";
            echo "<a href='index.php?pageController=tenant&action=update&id=" . $_GET["id"] . "' class='btnAction'>Modifier</a> ";
            echo "<a href='index.php?pageController=tenant&action=delete&id=" . $_GET["id"] . "' class='btnAction'>Supprimer</a>";
            echo "</div></div></div>";
        }
        ?>
    </main>

    <script>
        const modal = document.getElementById("tenantModal");
        const closeBtn = document.querySelector(".close-btn");

        if (closeBtn) {
            closeBtn.addEventListener("click", () => {
                modal.style.display = "none";
            });

            window.addEventListener("click", e => {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
        }
    </script>