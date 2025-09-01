<?php
$message = $_SESSION["message"];
$_SESSION["message"] = "";
$owner = new Owners();
$liste = $owner->read();
?>

<link rel="stylesheet" href="./css/owner.css">
<main class="main-content">
    <h1>Bienvenue <span class="admin">Admin</span>, vous êtes bien connecté en tant qu'administrateur.</h1>

    <div class="search-container">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" placeholder="Recherche..." class="search-input" />
    </div>

    <div class="title-section">
        <h2 class="sub-title">Liste des propriétaires (<?= count($liste) ?>)</h2>
        <a href="index.php?pageController=owner&action=create" class="btnAction">CRÉER UN PROPRIÉTAIRE</a>
    </div>

    <?php if (!empty($message)) : ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if (empty($_GET["id"])): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Aider</th>
                        <th>Infos</th>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($liste as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['firstName']) ?></td>
                            <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><a href="index.php?pageController=owner&action=help&id=<?= $row["ownerId"] ?>"><i class="fa-solid fa-hands-helping"></i></a></td>
                            <td><a href="index.php?pageController=owner&action=display&id=<?= $row["ownerId"] ?>" class="info-link"><i class="fa-solid fa-info"></i></a></td>
                            <td><a href="index.php?pageController=owner&action=update&id=<?= $row["ownerId"] ?>" class="edit-link"><i class="fa-solid fa-pen-to-square"></i></a></td>
                            <td><a href="index.php?pageController=owner&action=delete&id=<?= $row["ownerId"] ?>" class="delete-link"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <?php $ownerInfo = $owner->read($_GET["id"]); ?>
        <div class="owner-details">
            <h2>Informations sur le propriétaire sélectionné :</h2>
            <p><strong>Nom :</strong> <?= $ownerInfo["name"] ?></p>
            <p><strong>Prénom :</strong> <?= $ownerInfo["firstName"] ?></p>
            <p><strong>Email :</strong> <?= $ownerInfo["email"] ?></p>
            <p><strong>Téléphone :</strong> <?= $ownerInfo["phoneNumber"] ?></p>
            <p><strong>Entreprise :</strong> <?= $ownerInfo["company"] ?></p>
            <p><strong>Adresse :</strong> <?= $ownerInfo["address"] ?></p>
            <p><strong>Complément d'adresse :</strong> <?= $ownerInfo["additionalAddress"] ?></p>
            <p><strong>Ville :</strong> <?= $ownerInfo["cityName"] ?></p>
            <p><strong>Code postal :</strong> <?= $ownerInfo["postalCode"] ?></p>
            <p><strong>Pays :</strong> <?= $ownerInfo["country"] ?></p>
            <p><strong>IBAN :</strong> <?= $ownerInfo["iban"] ?></p>
            <p><strong>BIC :</strong> <?= $ownerInfo["bic"] ?></p>
            <p><strong>Genre :</strong> <?= $ownerInfo["gender"] ?></p>
        </div>
    <?php endif; ?>
</main>
