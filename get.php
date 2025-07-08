<?php
$templateId = 'e0379893-9c64-444e-8a25-ff4a84e77242';
$token = 'dcdc446c-9249-4a97-9bae-37ef79037e14';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://esignatures.com/api/templates/{$templateId}?token={$token}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("Erreur cURL : " . curl_error($ch));
}

curl_close($ch);

$data = json_decode($response, true);

echo "📋 Placeholders trouvés dans le template :\n";
print_r($data['data']['placeholder_fields'] ?? []);
