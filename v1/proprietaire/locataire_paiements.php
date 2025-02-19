<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php'; 

// Vérifiez que l'utilisateur est connecté et est un proprietaire
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'proprietaire') {
    header("Location: /proprietaire/connexion_proprietaire.php"); // Redirige s'il n'est pas connecté en tant que proprio
    exit();
}

if (!isset($_GET['locataire_id'])) {
    echo "Identifiant du locataire non spécifié.";
    exit();
}

$locataire_id = $_GET['locataire_id'];

// Récupérer les paiements pour le locataire spécifié
$sql = "
    SELECT p.*, b.bail_id, l.nom AS nom_locataire, l.prenom AS prenom_locataire
    FROM paiements p
    LEFT JOIN baux b ON p.bail_id = b.bail_id
    LEFT JOIN locataires l ON b.locataire_id = l.locataire_id
    WHERE l.locataire_id = :locataire_id AND p.statut = 'payé'
    ORDER BY p.mois";

$stmt = $db->prepare($sql);
$stmt->bindParam(':locataire_id', $locataire_id, PDO::PARAM_INT);
$stmt->execute();
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérification rapide pour s'assurer que des paiements ont été trouvés
if (empty($paiements)) {
    echo "Aucun paiement trouvé pour ce locataire.";
    exit();
}

// Traitement de la modification de paiement via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paiement_id'])) {
    $paiement_id = $_POST['paiement_id'];
    $method = $_POST['method'];
    $date = $_POST['date'];
    $amount = $_POST['amount'];

    // Mise à jour du paiement dans la base de données
    $stmt_update = $db->prepare("UPDATE paiements SET methode = ?, date = ? WHERE paiement_id = ?");
    $stmt_update->execute([$method, $date, $paiement_id]);

    if ($stmt_update->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Le paiement a été modifié avec succès.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Aucune modification détectée ou erreur lors de la mise à jour.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Paiements</title>
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
	<style>
		/* Reset and Global Styles */
body, h2, p, form, input, a, table, th, td, button {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    color: #213c4a; /* Dark Gray/Black */
    box-sizing: border-box;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #e9eef1; /* Light Teal background */
    padding: 20px;
}

/* Container Styles */
.container {
    background: #ffffff; /* Off-White */
    border-radius: 4px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 1000px;
    width: 100%;
    padding: 20px;
    text-align: center;
}

/* Navigation Styles */
nav ul {
    list-style-type: none;
    padding: 0;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-around;
    background-color: #213c4a; /* Dark Gray/Black */
    border-radius: 4px;
}

nav ul li {
    position: relative;
}

nav ul li a {
    color: #ffffff; /* White */
    text-decoration: none;
    padding: 10px 20px;
    display: block;
}

nav ul li a:hover {
    background-color: #002011; /* Dark Teal */
}

nav ul li ul {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #213c4a; /* Dark Gray/Black */
    border-radius: 4px;
    list-style-type: none;
    padding: 0;
    margin: 0;
    z-index: 1000;
}

nav ul li:hover ul {
    display: block;
}

nav ul li ul li {
    width: 100%;
}

nav ul li ul li a {
    padding: 10px 20px;
}

/* Heading Styles */
h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #002011; /* Dark Teal */
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #213c4a; /* Dark Gray/Black */
    color: #ffffff; /* White */
}

td a {
    color: #587f96; /* Light Teal */
    text-decoration: none;
}

td a:hover {
    text-decoration: underline;
}

.action-buttons button {
    background: none;
    border: none;
    color: #213c4a; /* Dark Gray/Black */
    cursor: pointer;
    font-size: 16px;
    margin-right: 5px;
}

.action-buttons button:hover {
    color: #002011; /* Dark Teal */
}

.file-icon {
    color: #587f96; /* Light Teal */
}

.file-icon:hover {
    color: #213c4a; /* Dark Gray/Black */
}

/* Export Button */
.export-button {
    margin-top: 20px;
    padding: 10px 20px;
    border-radius: 4px;
    background-color: #213c4a; /* Dark Gray/Black */
    color: #ffffff; /* White */
    border: none;
    cursor: pointer;
}

.export-button:hover {
    background-color: #002011; /* Dark Teal */
}

/* Style for modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    position: absolute;
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    border-radius: 4px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Responsive Styles */
@media (max-width: 600px) {
    .container {
        padding: 15px;
    }

    nav ul {
        flex-direction: column;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
    }

    .modal-content {
        width: 90%;
    }
}
	</style>
   
</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li>
                    <a href="proprietaire_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a>
                    <ul>
                        <li><a href="garage.php" title="Gérer vos garages"><i class="fas fa-warehouse"></i> Garages</a></li>
                        <li><a href="locataires.php" title="Gérer vos locataires"><i class="fas fa-users"></i> Locataires</a></li>
                        <li><a href="liste_baux.php" title="Gérer les baux"><i class="fas fa-file-contract"></i> Baux</a></li>
                        <li><a href="liste_paiements.php" title="Gérer les paiements"><i class="fas fa-money-check-alt"></i> Paiements</a></li>
                    </ul>
                </li>
                <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>

        <h2><i class="fas fa-list"></i> Liste des Paiements pour <?= htmlspecialchars($paiements[0]['nom_locataire'] . ' ' . $paiements[0]['prenom_locataire']); ?></h2>
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-calendar-alt"></i> Mois</th>
                    <th><i class="fas fa-euro-sign"></i> Montant</th>
                    <th><i class="fas fa-calendar-day"></i> Date de Paiement</th>
                    <th><i class="fas fa-credit-card"></i> Méthode de Paiement</th>
                    <th><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paiements as $paiement): ?>
                    <tr>
                        <td><?= htmlspecialchars($paiement['mois']); ?></td>
                        <td><?= htmlspecialchars($paiement['montant']); ?></td>
                        <td><?= htmlspecialchars($paiement['date']); ?></td>
                        <td><?= htmlspecialchars($paiement['methode']); ?></td>
                        <td class="action-buttons">
                            <button type="button" title="Modifier le paiement" onclick="openPaiementModal('<?= $paiement['paiement_id']; ?>', '<?= $paiement['montant']; ?>', '<?= $paiement['statut']; ?>', '<?= $paiement['methode']; ?>', '<?= $paiement['date']; ?>')"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" title="Annuler le paiement" class="cancel-payment-button" data-paiement-id="<?= $paiement['paiement_id']; ?>"><i class="fas fa-times"></i></button>
                            <a href="generate_quittance.php?paiement_id=<?= $paiement['paiement_id']; ?>" target="_blank" title="Générer la quittance"><i class="fas fa-file-alt file-icon"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="export-button" onclick="exportCSV()"><i class="fas fa-file-export"></i> Export CSV</button>
        <br><br>
        <a href="javascript:history.go(-1)"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <!-- Modal pour modifier le paiement -->
    <div id="paiementModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePaiementModal()">&times;</span>
            <form id="paiementForm" method="post">
                <input type="hidden" name="paiement_id" id="paiement_id">
                <label for="method"><i class="fas fa-credit-card"></i> Méthode de Paiement :</label>
                <select id="method" name="method" required>
                    <option value="Espèces">Espèces</option>
                    <option value="Chèques">Chèques</option>
                    <option value="Carte bancaire">Carte bancaire</option>
                    <option value="Virement">Virement</option>
                    <option value="Traite">Traite</option>
                    <option value="Avoir">Avoir</option>
                    <option value="Prélèvement">Prélèvement</option>
                    <option value="Paypal">Paypal</option>
                </select><br><br>
                <label for="date"><i class="fas fa-calendar-alt"></i> Date de Paiement :</label>
                <input type="date" id="date" name="date" required><br><br>
                <label for="amount"><i class="fas fa-euro-sign"></i> Montant :</label>
                <input type="text" id="amount" name="amount" readonly><br><br>
                <input type="submit" value="Modifier le Paiement" id="submitButton">
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var closePaiementBtn = document.querySelector('#paiementModal .close');

            closePaiementBtn.onclick = closePaiementModal;

            window.onclick = function(event) {
                var paiementModal = document.getElementById('paiementModal');

                if (event.target == paiementModal) {
                    closePaiementModal();
                }
            };

            // Ajouter des gestionnaires d'événements pour les boutons d'annulation de paiement
            var cancelPaymentButtons = document.querySelectorAll('.cancel-payment-button');
            cancelPaymentButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var paiementId = this.getAttribute('data-paiement-id');
                    cancelPayment(paiementId);
                });
            });
        });

        function openPaiementModal(paiement_id, montant, statut, methode, date) {
            document.getElementById('paiement_id').value = paiement_id;
            document.getElementById('amount').value = montant;
            document.getElementById('method').value = methode;
            document.getElementById('date').value = date;

            document.getElementById('submitButton').value = 'Modifier le Paiement';
            document.getElementById('paiementModal').style.display = 'block';
        }

        function closePaiementModal() {
            document.getElementById('paiementModal').style.display = 'none';
        }

        function cancelPayment(paiementId) {
            if (confirm('Êtes-vous sûr de vouloir annuler ce paiement ?')) {
                $.ajax({
                    url: 'annuler_paiement.php',
                    type: 'POST',
                    data: { paiement_id: paiementId },
                    success: function(response) {
                        alert('Le paiement a été annulé avec succès.');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Erreur lors de l\'annulation du paiement.');
                    }
                });
            }
        }

        // Soumettre le formulaire de paiement via AJAX
        $('#paiementForm').on('submit', function(event) {
            event.preventDefault(); // Empêcher le rechargement de la page
            $.ajax({
                url: '',
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de la modification du paiement.');
                }
            });
        });

        // Fonction pour exporter les données en CSV
        function exportCSV() {
            window.location.href = 'export_paiements_csv.php?locataire_id=<?= $locataire_id ?>';
        }
    </script>
</body>
</html>
