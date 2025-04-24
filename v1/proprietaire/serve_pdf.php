<?php
// serve_pdf.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$bail_id = $_GET['id'] ?? null;
if (!$bail_id) {
    echo "Bail ID manquant.";
    exit();
}

// Chemin absolu vers le fichier PDF
$pdf_file_path = '/var/www/vhosts/bailgarage.fr/app.bailgarage.fr/pdfs/bail_confirmation_' . $bail_id . '.pdf';

if (!file_exists($pdf_file_path)) {
    echo "Fichier non trouvé.";
    exit();
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="bail_confirmation_' . $bail_id . '.pdf"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
@readfile($pdf_file_path);
?>
