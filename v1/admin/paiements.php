<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../includes/db.php';

// Vérifier s'il y a un message d'erreur
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Effacer le message après l'avoir affiché

// Vérifier s'il y a un message de succès
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']); // Effacer le message après l'avoir affiché

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../admin/connexion_admin.php");
    exit();
}

// Récupérer l'ID du propriétaire depuis l'URL
if (!isset($_GET['proprietaire_id'])) {
    die('Propriétaire ID non spécifié.');
}
$proprietaire_id = intval($_GET['proprietaire_id']);

// Récupérer les baux actifs
$stmt_baux = $db->prepare("
    SELECT b.bail_id, l.nom AS locataire_nom, l.prenom AS locataire_prenom, g.addresse, g.numero_garage, b.total_mensuel
    FROM baux b
    JOIN locataires l ON b.locataire_id = l.locataire_id
    JOIN garages g ON b.garage_id = g.garage_id
    WHERE g.proprietaire_id = ? AND b.status = 'active'
");
$stmt_baux->execute([$proprietaire_id]);
$baux = $stmt_baux->fetchAll(PDO::FETCH_ASSOC);

// Générer les paiements pour le mois en cours s'ils n'existent pas déjà
$formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Paris', IntlDateFormatter::GREGORIAN, 'MMMM');
$current_month = $formatter->format(new DateTime());

foreach ($baux as $bail) {
    $stmt_check_paiement = $db->prepare("SELECT COUNT(*) FROM paiements WHERE bail_id = ? AND mois = ?");
    $stmt_check_paiement->execute([$bail['bail_id'], $current_month]);
    $paiement_exists = $stmt_check_paiement->fetchColumn();

    if (!$paiement_exists) {
        $stmt_insert_paiement = $db->prepare("INSERT INTO paiements (bail_id, mois, montant) VALUES (?, ?, ?)");
        $stmt_insert_paiement->execute([$bail['bail_id'], $current_month, $bail['total_mensuel']]);
    }
}

// Récupérer les paiements du mois en cours
$stmt_paiements = $db->prepare("
    SELECT p.paiement_id, p.bail_id, p.statut, p.montant, p.methode, p.date, l.nom AS locataire_nom, l.prenom AS locataire_prenom, g.addresse, g.numero_garage
    FROM paiements p
    JOIN baux b ON p.bail_id = b.bail_id
    JOIN locataires l ON b.locataire_id = l.locataire_id
    JOIN garages g ON b.garage_id = g.garage_id
    WHERE p.mois = ? AND g.proprietaire_id = ?
");
$stmt_paiements->execute([$current_month, $proprietaire_id]);
$paiements = $stmt_paiements->fetchAll(PDO::FETCH_ASSOC);

// Calculer le total des paiements en attente
$total_paiements_en_attente = 0;
foreach ($paiements as $paiement) {
    if ($paiement['statut'] == 'impayé') {
        $total_paiements_en_attente += $paiement['montant'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['paiement_id']) && isset($_POST['method']) && isset($_POST['date']) && isset($_POST['amount'])) {
        $paiement_id = $_POST['paiement_id'];
        $method = $_POST['method'];
        $date = $_POST['date'];
        $amount = $_POST['amount'];

        // Marquer le paiement comme payé dans la base de données
        $stmt_update_paiement = $db->prepare("UPDATE paiements SET statut = 'payé', methode = ?, date = ? WHERE paiement_id = ?");
        $stmt_update_paiement->execute([$method, $date, $paiement_id]);
        
    } elseif (isset($_POST['action']) && $_POST['action'] === 'validate_selected') {
        $paiement_ids = $_POST['paiement_ids'];
        $methods = $_POST['methods'];
        $dates = $_POST['dates'];

        foreach ($paiement_ids as $index => $paiement_id) {
            $method = $methods[$index];
            $date = $dates[$index];

            // Marquer chaque paiement comme payé dans la base de données
            $stmt_update_paiement = $db->prepare("UPDATE paiements SET statut = 'payé', methode = ?, date = ? WHERE paiement_id = ?");
            $stmt_update_paiement->execute([$method, $date, $paiement_id]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi des Paiements</title>
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
</head>
<body>
    <div class="container">
         <nav>
            <ul> 
                <li>
                    <a href="admin_dash.php" title="Retour à l'accueil"><i class="fas fa-home"></i> Accueil</a>
                </li>
                <li><a href="profil.php" title="Voir et modifier votre profil"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="../includes/deconnexion.php" title="Se déconnecter de votre compte"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>  
            </ul>
        </nav>
        <div class="messages-container">
    <?php if (!empty($error_message)): ?>
        <div id="error-message" class="error-message"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div id="success-message" class="success-message"><?= $success_message ?></div>
    <?php endif; ?>
</div>

        
        <div class="container-paiements">
            
            <label for="filter">Afficher uniquement les impayés :</label>
            <input type="checkbox" id="filterCheckbox" onchange="filterPaiements()" checked><br><br>
            
            <h3>Suivi des Paiements - <?= ucfirst($current_month) . ' ' . date('Y') ?></h3>
            
            <?php if (count($paiements) > 0): ?>
                <form id="paiementsForm" action="" method="post">
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
						
                        <div class="dropdown" >
                            <label for="globalMethod">Méthode de Paiement :</label>
                            <select id="globalMethod" name="globalMethod" required>
                                <option value="Espèces">Espèces</option>
                                <option value="Chèques">Chèques</option>
                                <option value="Carte bancaire">Carte bancaire</option>
                                <option value="Virement" selected>Virement</option>
                                <option value="Traite">Traite</option>
                                <option value="Avoir">Avoir</option>
                                <option value="Prélèvement">Prélèvement</option>
                                <option value="Paypal">Paypal</option>
                            </select>
                        </div>
						
                        <div>
                            <label for="globalDate">Date de Paiement :</label>
                            <input type="date" id="globalDate" name="globalDate" required>
                        </div>
						
                        <button type="button" onclick="validateSelectedPaiements()">Valider les paiements sélectionnés</button>
                    </div>
					
					<br>
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <h4 style="margin: 0;">Total des paiements en attente : <span style="color: red;" id="totalEnAttente"><?= number_format($total_paiements_en_attente, 2) ?> €</span></h4>
                        <h4 style="margin: 0;">Total des lignes sélectionnées : <span style="color: red;" id="totalLignesSelectionnees">0.00 €</span></h4>
                    </div>
                    
                    <table id="paiementsTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" onclick="toggleCheckboxes(this); updateTotalSelectionne();"></th>
								<th>Id</th>
                                <th>Locataire</th>
                                <th>Num Garage</th>
                                <th>Montant</th>
                                <th>Payé</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paiements as $paiement): ?>
                                <tr class="<?= $paiement['statut'] == 'impayé' ? 'impaye' : 'paye' ?>">
                                    <td>
                                        <?php if ($paiement['statut'] == 'impayé'): ?>
                                            <input type="checkbox" class='payment-checkbox' name="paiement_ids[]" value="<?php echo $paiement['paiement_id']; ?>">
                                        <?php endif; ?>
                                    </td>
									<td><?= htmlspecialchars($paiement['paiement_id']); ?></td>
                                    <td><?= htmlspecialchars($paiement['locataire_nom'] . ' ' . $paiement['locataire_prenom']); ?></td>
                                    <td><a href="garage.php" title="Garage"><?= htmlspecialchars($paiement['numero_garage']); ?></a></td>
                                    <td class="montant"><?= htmlspecialchars($paiement['montant']); ?> €</td>
                                    <td>
                                        <?php if ($paiement['statut'] == 'payé'): ?>
                                            <span style="color: green; cursor: pointer;" title="Date de paiement: <?php echo date('d/m/Y', strtotime($paiement['date'])); ?>, Méthode de paiement: <?php echo $paiement['methode']; ?>">✔</span>
                                        <?php elseif ($paiement['statut'] == 'impayé'): ?>
                                            <span style="color: red;">✘</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" title="Modifier les informations du paiement" onclick="openPaiementModal('<?php echo $paiement['paiement_id']; ?>', '<?php echo $paiement['montant']; ?>', '<?php echo $paiement['statut']; ?>', '<?php echo $paiement['methode']; ?>', '<?php echo $paiement['date']; ?>')"><i class="fas fa-pencil-alt"></i></button>
                                        <?php if ($paiement['statut'] == 'payé'): ?>
                                            <button type="button" title="Annuler le paiement" class="cancel-payment-button" data-paiement-id="<?php echo $paiement['paiement_id']; ?>"><i class="fas fa-times"></i></button>
                                        <?php endif; ?>
                                        
                                        <?php if ($paiement['statut'] == 'impayé'): ?>
                                            <button type="button" title="Envoyer un mail de relance" onclick="openEmailModal('<?php echo $paiement['paiement_id']; ?>')"><i class="fas fa-envelope"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                </form>
			<a href="historique_paiements.php" title="Cliquez ici pour voir l'ensemble des paiements reçus"><i class="fas fa-history"></i>
Historique des paiements</a>
            <?php else: ?>
                <p>Aucun paiement trouvé pour ce mois.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal pour modifier le paiement -->
    <div id="paiementModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePaiementModal()">&times;</span>
            <form id="paiementForm" action="" method="post">
                <input type="hidden" name="paiement_id" id="paiement_id">
				<div class="dropdown">
                <label for="method">Méthode de Paiement :</label>
                <select id="method" name="method" required>
                    <option value="Espèces">Espèces</option>
                    <option value="Chèques">Chèques</option>
                    <option value="Carte bancaire">Carte bancaire</option>
                    <option value="Virement">Virement</option>
                    <option value="Traite">Traite</option>
                    <option value="Avoir">Avoir</option>
                    <option value="Prélèvement">Prélèvement</option>
                    <option value="Paypal">Paypal</option>
                </select>
				</div>
                <label for="date">Date de Paiement :</label>
                <input type="date" id="date" name="date" required><br>
                <label for="amount">Montant :</label>
                <input type="text" id="amount" name="amount" readonly><br><br>
                <input type="submit" value="Enregistrer le Paiement" id="submitButton">
            </form>
        </div>
    </div>

    <!-- Modal pour modifier l'email de relance -->
    <div id="emailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEmailModal()">&times;</span>
            <h2>Modifier le contenu de l'email de relance</h2>
            <form id="emailForm" method="post" action="relance.php">
                <input type="hidden" id="modalPaiementId" name="paiement_id">
                <label for="modalEmailSubject">Objet de l'email :</label>
                <input type="text" id="modalEmailSubject" name="emailSubject" style="width: 100%;"><br><br>
                <textarea id="modalEmailContent" name="emailContent" rows="15" style="width: 100%;"></textarea><br>
                <label for="sendCopy">Envoyer une copie à moi-même :</label>
                <input type="checkbox" id="sendCopy" name="sendCopy"><br><br>
                <button type="button" onclick="sendEmail()">Envoyer</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var closePaiementBtn = document.querySelector('#paiementModal .close');
            var closeEmailBtn = document.querySelector('#emailModal .close');

            closePaiementBtn.onclick = closePaiementModal;
            closeEmailBtn.onclick = closeEmailModal;

            window.onclick = function(event) {
                var paiementModal = document.getElementById('paiementModal');
                var emailModal = document.getElementById('emailModal');

                if (event.target == paiementModal) {
                    closePaiementModal();
                }
                if (event.target == emailModal) {
                    closeEmailModal();
                }
            };

            // Appliquer le filtre lorsque la page est chargée
            filterPaiements();

            // Ajouter des gestionnaires d'événements pour les boutons d'annulation de paiement
            var cancelPaymentButtons = document.querySelectorAll('.cancel-payment-button');
            cancelPaymentButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var paiementId = this.getAttribute('data-paiement-id');
                    cancelPayment(paiementId);
                });
            });

            // Initialiser la date du jour pour le champ date
            var inputDate = document.getElementById("date");
            var currentDate = new Date();
            var formattedDate = currentDate.toISOString().split('T')[0];
            inputDate.value = formattedDate;
        });

        function openPaiementModal(paiement_id, montant, statut, methode, date) {
            document.getElementById('paiement_id').value = paiement_id;
            document.getElementById('amount').value = montant;
            if (statut === 'impayé') {
                document.getElementById('method').value = 'Virement';
                document.getElementById('date').value = new Date().toISOString().split('T')[0];
            } else {
                document.getElementById('method').value = methode;
                document.getElementById('date').value = date ? date : new Date().toISOString().split('T')[0];
            }
            document.getElementById('submitButton').value = (statut === 'impayé') ? 'Enregistrer le paiement' : 'Modifier le Paiement';
            document.getElementById('paiementModal').style.display = 'block';
        }

        function closePaiementModal() {
            document.getElementById('paiementModal').style.display = 'none';
        }

        function openEmailModal(paiement_id) {
            document.getElementById('modalPaiementId').value = paiement_id;

            $.ajax({
                url: 'edit_email.php',
                type: 'GET',
                data: { paiement_id: paiement_id },
                dataType: 'json',
                success: function(data) {
                    document.getElementById('modalEmailSubject').value = data.subject;
                    $('#modalEmailContent').summernote({
                        height: 300,
                        minHeight: 300,
                        maxHeight: 500,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear', 'italic']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ]
                    }).summernote('code', data.content);

                    document.getElementById('emailModal').style.display = 'block';
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de la récupération du contenu de l\'email.');
                }
            });
        }

        function closeEmailModal() {
            $('#modalEmailContent').summernote('destroy');
            document.getElementById('emailModal').style.display = 'none';
        }

        function sendEmail() {
            var emailForm = document.getElementById('emailForm');
            emailForm.submit();
        }

        function filterPaiements() {
            var checkBox = document.getElementById("filterCheckbox");
            var rows = document.querySelectorAll("#paiementsTable tbody tr");
            rows.forEach(function(row) {
                if (checkBox.checked) {
                    if (row.classList.contains('paye')) {
                        row.style.display = "none";
                    } else {
                        row.style.display = "table-row";
                    }
                } else {
                    row.style.display = "table-row";
                }
            });
        }
        
        function validateSelectedPaiements() {
            var form = document.getElementById('paiementsForm');
            var selected = Array.from(document.querySelectorAll('input[name="paiement_ids[]"]:checked')).map(checkbox => checkbox.value);

            if (selected.length > 0) {
                var globalMethod = document.getElementById('globalMethod').value;
                var globalDate = document.getElementById('globalDate').value;

                selected.forEach(function(id) {
                    var inputMethod = document.createElement('input');
                    inputMethod.type = 'hidden';
                    inputMethod.name = 'methods[]';
                    inputMethod.value = globalMethod;
                    form.appendChild(inputMethod);

                    var inputDate = document.createElement('input');
                    inputDate.type = 'hidden';
                    inputDate.name = 'dates[]';
                    inputDate.value = globalDate;
                    form.appendChild(inputDate);
                });

                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'validate_selected';
                form.appendChild(actionInput);

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: $(form).serialize(),
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Erreur lors de la validation des paiements.');
                    }
                });
            } else {
                alert('Veuillez sélectionner au moins un paiement.');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var checkboxes = document.querySelectorAll('input[name="paiement_ids[]"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateTotalSelectionne);
            });
        });

        function updateTotalSelectionne() {
            var totalSelectionne = 0;
            var selectedCheckboxes = document.querySelectorAll('input[name="paiement_ids[]"]:checked');

            selectedCheckboxes.forEach(function(checkbox) {
                var row = checkbox.closest('tr');
                var montantCell = row.querySelector('.montant');
                var montantText = montantCell.textContent.trim().replace(' €', '');
                var montant = parseFloat(montantText);
                totalSelectionne += montant;
            });

            var totalLignesSelectionneesElement = document.getElementById('totalLignesSelectionnees');
            if (totalLignesSelectionneesElement) {
                totalLignesSelectionneesElement.textContent = totalSelectionne.toFixed(2) + ' €';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var inputDate = document.getElementById("date");
            var currentDate = new Date();
            var formattedDate = currentDate.toISOString().split('T')[0];
            inputDate.value = formattedDate;
        });

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

        function toggleCheckboxes(masterCheckbox) {
            var checkboxes = document.querySelectorAll('.payment-checkbox');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = masterCheckbox.checked;
            }
        }

        // Soumettre le formulaire de paiement via AJAX
        $('#paiementForm').on('submit', function(event) {
            event.preventDefault(); // Empêcher le rechargement de la page
            $.ajax({
                url: '',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de l\'enregistrement du paiement.');
                }
            });
        });
		
		document.addEventListener('DOMContentLoaded', function() {
            // Définir la date du jour
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0'); // Les mois sont de 0 à 11
            const year = today.getFullYear();
            
            const formattedDate = `${year}-${month}-${day}`; // Format pour l'input type="date"
            document.getElementById('globalDate').value = formattedDate;
        });
		
		 document.addEventListener('DOMContentLoaded', function() {
        // Masquer le message d'erreur après 5 secondes
        var errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 5000); // 5000 millisecondes = 5 secondes
        }

        // Masquer le message de succès après 5 secondes
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 5000); // 5000 millisecondes = 5 secondes
        }
    });
    </script>
</body>
</html>