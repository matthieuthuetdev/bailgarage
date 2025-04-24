<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../includes/db.php';
require_once('../includes/tcpdf/tcpdf.php');
require '../includes/phpmailer/src/Exception.php';
require '../includes/phpmailer/src/PHPMailer.php';
require '../includes/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Vérifier que l'utilisateur connecté est un propriétaire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'proprietaire') {
    header("Location: ../connexion_proprietaire.php");
    exit();
}

// ID du bail
$bail_id = $_POST['bail_id'] ?? null;
if (!$bail_id) {
    $_SESSION['error_message'] = "Bail ID manquant.";
    header("Location: docs_bail.php?id=$bail_id");
    exit();
}

// Récupérer les informations du bail
$stmt = $db->prepare("SELECT * FROM baux WHERE bail_id = ?");
$stmt->bindParam(1, $bail_id, PDO::PARAM_INT);
$stmt->execute();
$bail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bail) {
    $_SESSION['error_message'] = "Bail non trouvé.";
    header("Location: docs_bail.php?id=$bail_id");
    exit();
}

// Récupérer les informations du locataire
$stmt_locataire = $db->prepare("SELECT * FROM locataires WHERE locataire_id = ?");
$stmt_locataire->bindParam(1, $bail['locataire_id'], PDO::PARAM_INT);
$stmt_locataire->execute();
$locataire = $stmt_locataire->fetch(PDO::FETCH_ASSOC);

// Récupérer les informations du garage
$stmt_garage = $db->prepare("SELECT * FROM garages WHERE garage_id = ?");
$stmt_garage->bindParam(1, $bail['garage_id'], PDO::PARAM_INT);
$stmt_garage->execute();
$garage = $stmt_garage->fetch(PDO::FETCH_ASSOC);

if (!$garage) {
    $_SESSION['error_message'] = "Garage non trouvé.";
    header("Location: docs_bail.php?id=$bail_id");
    exit();
}

// Récupérer les informations du propriétaire
$proprietaire_id = $garage['proprietaire_id'];
$stmt_proprietaire = $db->prepare("SELECT * FROM proprietaires WHERE proprietaire_id = ?");
$stmt_proprietaire->bindParam(1, $proprietaire_id, PDO::PARAM_INT);
$stmt_proprietaire->execute();
$proprietaire = $stmt_proprietaire->fetch(PDO::FETCH_ASSOC);

if (!$proprietaire) {
    $_SESSION['error_message'] = "Propriétaire non trouvé.";
    header("Location: docs_bail.php?id=$bail_id");
    exit();
}

// Créer le PDF en mémoire
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', true);
$pdf->setTitle('Bail');
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = '
<style>
        body, h2, h3, p, div {
            font-family: "Arial", sans-serif;
            color: #213c4a; /* Dark Gray/Black */
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            text-align: center;
            margin-top: 0;
        }

        h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #002011; /* Dark Teal */
        }

        p {
            margin: 5px 0;
        }
		span {
			color: #00008B;
		}

        .box-input{
			border: 1px solid #ccc;
			
			border-radius: 4px;
			background-color: #fefdfb; /* Off-White */
			display: inline-block; /* Ensures it fits content and stays on the same line */
			margin: 0; /* Remove margin */
			box-sizing: border-box; /* Ensure padding and border are included in width/height */
			left-padding:5px;
		       }



        .signature {
            margin-top: 20px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 20px auto 5px auto;
        }

        .signature-text {
            font-size: 12px;
            text-align: center;
        }

        .signature-position {
            font-size: 12px;
            margin-top: 10px;
            color: orange;
            text-align: center;
        }

        .page-number {
            text-align: right;
            font-size: 12px;
            color: #587f96; /* Light Teal */
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #213c4a;
        }

        .underline {
            text-decoration: underline;
        }

        .rayer {
            text-decoration: line-through;
        }

        .container {
            text-align: center;
            margin: 0 auto;
            width: 100%;
            max-width: 800px;
			
        }

        .page-content {
            margin: 0 auto;
            width: 100%;
            max-width: 800px;
            padding: 10px;
        }

        .flex-item {
            margin-right: 10px; /* Adjust this to control spacing between text and box */
        }


		
    </style>


  <h1>Contrat de location de parking/garage/box</h1>
  <p>Soumis aux dispositions du code civil article 1708 et suivants</p>



    <h2>I. DESIGNATION DES PARTIES</h2>
    <p>Le contrat est conclu entre les deux parties désignées ci-dessous :</p>
    <p>
    <div class="box-input">
      <p> ' . htmlspecialchars($proprietaire['nom']) . ' ' . htmlspecialchars($proprietaire['prenom']) . ' </p>
      <p>' . htmlspecialchars($proprietaire['addresse']) . ' ,' . htmlspecialchars($proprietaire['CP']) . ' ' . htmlspecialchars($proprietaire['ville']) . ' ' . htmlspecialchars($proprietaire['pays']) . '  </p>
      <p>' . htmlspecialchars($proprietaire['email']) . '</p>
	  <p>' . htmlspecialchars($proprietaire['telephone']) . '</p>
    </div>
	Dénommé ci-après « le bailleur » ;
	</p>

    <p>
    <div class="box-input">
        <p>' . htmlspecialchars($locataire['nom']) . ' ' . htmlspecialchars($locataire['prenom']) . ' </p>
        <p>' . htmlspecialchars($locataire['addresse']) . ' ,' . htmlspecialchars($locataire['CP']) . ' ' . htmlspecialchars($locataire['ville']) . ' </p>
        <p>' . htmlspecialchars($locataire['email']) . '</p>
        <p>' . htmlspecialchars($locataire['telephone']) . '</p>
    </div>
	Dénommé ci-après « le locataire » ;
	</p>

    <h2>II. OBJET DU CONTRAT</h2>
    <p>Le présent contrat a pour objet la location du
    <div class="box-input">
        <p>' . htmlspecialchars($garage['addresse']) . ' </p>
        <p> Désigné par le numéro ou la lettre : ' . htmlspecialchars($garage['numero_garage']) . ' </p>

        <p> Dune surface de: ' . htmlspecialchars($garage['surface']) . ' </p>

        <p> Ajouter un descriptif précis au besoin : ' . htmlspecialchars($garage['commentaire']) . ' </p>
	</div>
	 Dénommé ci-après « le local » ;
	</p>
   

    <div class="signature">
      <div class="signature-line"></div>
      
    </div>
  

  <div class="page-content">
    <h2>III. DUREE DU CONTRAT</h2>
    <p>Le présent contrat prend effet à partir du <span> '.htmlspecialchars($bail['date_debut']).' </span> pour une durée de
    <span>'. htmlspecialchars($bail['duree']).'</span> mois.</p>
    <p>Il est reconduit par tacite reconduction pour une période identique. Le locataire et le
      bailleur peuvent résilier le présent contrat par lettre recommandée avec accusé de
      réception à tout moment en respectant un préavis de 1 mois, sans justification daucun motif.</p>

    <h2>IV. PRIX ET CHARGES</h2>
    <p>Le montant mensuel du loyer est fixé à <span> '. htmlspecialchars($bail['total_mensuel']).'</span> € soit <span>'. htmlspecialchars($bail['total_mensuel_lettres']).'</span> euros
      (en lettres) par mois</p>

    <p>Le premier loyer est dû à partir du <span>'. htmlspecialchars($bail['date_debut']).'</span>
     Il est versé ce jour par le locataire au bailleur.
      Son montant est de <span>'. htmlspecialchars($bail['prorata']).'</span> €</p>
    <p>Les charges provisionnelles sont de <span>'. htmlspecialchars($bail['montant_charges']).'</span> € soit
      <span>'. htmlspecialchars($bail['montant_charges_lettres']).'</span> euros (en lettres) par mois. Elles seront régularisées annuellement. Les charges
      qui peuvent incomber au locataire seront payées par le locataire.</p>
    <p>Le paiement seffectue par avance, par virement bancaire sur le compte du bailleur le 1er
      de chaque mois.</p>
    <div class="box-input">
      <p> IBAN: '. htmlspecialchars($proprietaire['iban']).'</p>
	  <p> BIC/SWIFT:'. htmlspecialchars($proprietaire['bic']).' </p>
    </div>
    <div class="box-input">
     <p> Référence de virement souhaitée:'. htmlspecialchars($garage['reference']).' </p>
    </div>
 

    <h2>V. REVISION DU LOYER</h2>
    <p>La révision du loyer se fera chaque année à la date anniversaire de la signature du
      contrat. Laugmentation du loyer ne peut être supérieure à la variation de lindice du coût
      de la construction (ICC) publié par lINSEE.</p>

    <h2>VI. DEPOT DE GARANTIE</h2>
    <p>Le locataire verse au moment de la signature du contrat un dépôt de garantie égal à 1
      mois de loyer, soit <span>'. htmlspecialchars($bail['caution']).'</span> € ou
      <span>'. htmlspecialchars($bail['caution_lettres']).'</span> euros € (en lettres). 
	  Le dépôt de garantie sera rendu au locataire au plus tard 2 mois après son départ, déduction faite des
      loyers qui resteraient à payer et des réparations locatives et sous réserve de justification
      de paiement des impôts locatifs. Le dépôt de garantie nest pas productif dintérêts.</p>

    <h2>VII. ACCES</h2>
    <p>Il a été remis au locataire par le bailleur <span>'. htmlspecialchars($bail['nombre_de_clefs']).'</span> clé(s) et
      </label> <span>'. htmlspecialchars($bail['nombre_de_bips']).'</span> télécommande (s) du portail
      </label>
    </p>
	
  </div>

  <div class="page-content">
    <h2>VIII. OBLIGATIONS DU LOCATAIRE</h2>
    <p>Le locataire sengage à maintenir en état le garage, notamment par le graissage ou
      huilage régulier des charnières, des glissières et de la serrure de la porte de garage.
      Le parking est loué à des fins de stationnement. Il ne peut être sous-loué sans
      laccord écrit préalable du bailleur. Le parking ne peut pas être utilisé comme
      local professionnel, commercial, artisanal ou atelier.</p>
    <p>La propreté du sol et des murs du garage est de la responsabilité du locataire. Le local
      devra être rendu en parfait état de propreté. Aucun percement de mur, démolition ou
      aménagement ne peut être fait sans laccord écrit du bailleur.</p>
    <p>Le locataire sengage de ne pas stocker de lhuile, de lessence ou tout produit pouvant
      provoquer un incendie.</p>
    <p>En cas de chute de neige, le déneigement de laccès au local incombe au locataire. Tous
      les dommages dus au non-respect du contrat et/ou dune utilisation non conforme par le
      locataire ou un tiers habilité à user du local seront imputés au locataire.</p>
    <p>A défaut, le présent contrat sera automatiquement résilié</p>

    <h2>IX. CLAUSE RESOLUTOIRE</h2>
    <p>A défaut de paiement à échéance du loyer et des charges ou en cas de non-respect des
      clauses du présent contrat, et quinze jours après sommation de payer les sommes dues, y
      compris les frais, par lettre recommandée avec accusé de réception, le contrat sera résilié
      de plein droit.</p>

    <h2>X. ASSURANCE</h2>
    <p>Laccès au local et le stationnement du véhicule du locataire sont au risque du locataire.
      Le locataire est donc responsable des dommages ou pertes (vol, incendie...) causés par
      ces faits. Le locataire doit apporter la preuve quil a bien assuré le local à la demande du
      bailleur.</p>

    <h2>XI. VISITES</h2>
    <p>Le bailleur peut entrer dans le local à tout moment pour réparer des dégâts, en cas de
      danger ou pour présenter le local à un futur locataire.</p>

    <p>Fait à
      <span>'. htmlspecialchars($bail['fait_a']).'</span>
      le
      <span>'. htmlspecialchars($bail['fait_le']).'</span> </p>
     <p> En 2 exemplaires originaux, remis à chacune des parties.</p>

    <div class="signature">
      <div class="signature-line"></div>
      <div class="signature-text">Signature précédée de la mention « lu et approuvé »</div>
      <div class="signature-position">Le bailleur</div>
      <div class="signature-line"></div>
      <div class="signature-position">Le locataire</div>
    </div>
  </div>

  <div class="footer">
   
  </div>
</div>'; // Votre contenu HTML du PDF

$pdf->writeHTML($html, true, false, true, false, '');

// Générer le PDF en tant que chaîne de caractères
$pdfContent = $pdf->Output('bail_confirmation.pdf', 'S');

// Envoyer l'email
$email = $_POST['email'] ?? null;
$emailSubject = $_POST['emailSubject'] ?? 'Bail à signer';
$emailContent = $_POST['emailContent'] ?? '';
$sendCopy = isset($_POST['sendCopy']);

// Configure PHPMailer
$mail = new PHPMailer(true);
try {
    // Server settings
   		$mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lililizalopi2021@gmail.com';
        $mail->Password = 'esbb phvk adqu zzgm';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;


    // Recipients
    	$mail->setFrom('lililizalopi2021@gmail.com', 'Admin BailGarage');
        $mail->addAddress($email);

    if ($sendCopy) {
            $mail->addAddress($proprietaire_email);
        }

    // Attachments
    $mail->addStringAttachment($pdfContent, 'bail_confirmation.pdf');

    // Content
    $mail->isHTML(true);
    $mail->Subject = $emailSubject;
    $mail->Body = $emailContent;

    $mail->send();
    $_SESSION['success_message'] = 'Email envoyé avec succès.';
} catch (Exception $e) {
    $_SESSION['error_message'] = "L'email n'a pas pu être envoyé. Erreur de Mailer: {$mail->ErrorInfo}";
}

header("Location: docs_bail.php?id=$bail_id");
exit();
?>