<?php
$data = [
    "template_id" => "",
    "signers" => [
        ["name"=>"Matthieu THUET","email"=>"mthuet.pro@gmail.com"],
        ["name"=>"Sébastien SCHULLER","email"=>"sebastien@karedess.agency"]
    ],
    "placeholder_fields" => [
        ["api_key"=>"ownerName",              "value"=>"SCHULLER"],
        ["api_key"=>"ownerFirstName",        "value"=>"Sébastien"],
        ["api_key"=>"ownerAddress",          "value"=>"3 rue test"],
        ["api_key"=>"ownerAdditionalAddress","value"=>"Bâtiment A"],
        ["api_key"=>"ownerPostalCode",       "value"=>"68100"],
        ["api_key"=>"ownerCityName",         "value"=>"Mulhouse"],
        ["api_key"=>"ownerCountry",          "value"=>"France"],
        ["api_key"=>"ownerPhoneNumber",      "value"=>"0600000000"],
        ["api_key"=>"ownerEmail",            "value"=>"sebastien@karedess.agency"],
        ["api_key"=>"ownerIban",             "value"=>"FR7630001007941234567890185"],
        ["api_key"=>"ownerBic",              "value"=>"AGRIFRPPXXX"],

        ["api_key"=>"tenantName",            "value"=>"THUET"],
        ["api_key"=>"tenantFirstName",       "value"=>"Matthieu"],
        ["api_key"=>"tenantAddress",         "value"=>"3 rue de la betten"],
        ["api_key"=>"tenantAdditionalAddress","value"=>""],
        ["api_key"=>"tenantPostalCode",      "value"=>"68290"],
        ["api_key"=>"tenantCity",            "value"=>"Bourbach-le-bas"],
        ["api_key"=>"tenantCountry",         "value"=>"France"],
        ["api_key"=>"tenantPhoneNumber",     "value"=>"0611223344"],
        ["api_key"=>"tenantEmail",           "value"=>"mthuet.pro@gmail.com"],

        ["api_key"=>"garageAddress",         "value"=>"10 rue du Garage"],
        ["api_key"=>"garageAdditionalAddress","value"=>"Sous-sol"],
        ["api_key"=>"garagePostalCode",      "value"=>"68100"],
        ["api_key"=>"garageCityName",        "value"=>"Mulhouse"],
        ["api_key"=>"garageCountry",         "value"=>"France"],
        ["api_key"=>"garageNumber",          "value"=>"B12"],
        ["api_key"=>"garageSurface",         "value"=>"14 m²"],
        ["api_key"=>"garageNote",            "value"=>"Porte motorisée avec bip"],

        ["api_key"=>"leaseStartDate",        "value"=>"2025-07-01"],
        ["api_key"=>"leaseDuration",         "value"=>"12"],
        ["api_key"=>"leaseRentAmount",       "value"=>"120"],
        ["api_key"=>"leaseRentAmountLetters","value"=>"cent vingt"],
        ["api_key"=>"leaseFirstPaymentAmount","value"=>"120"],
        ["api_key"=>"leaseCharges",          "value"=>"10"],
        ["api_key"=>"leaseChargesLetters",   "value"=>"dix"],
        ["api_key"=>"leaseReference",        "value"=>"LOYER-GARAGE-B12"],
        ["api_key"=>"numberOfKey",           "value"=>"1"],
        ["api_key"=>"numberOfBeep",          "value"=>"1"],
        ["api_key"=>"leasesMadeIn",          "value"=>"Mulhouse"],
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://esignatures.com/api/contracts?token=dcdc446c-9249-4a97-9bae-37ef79037e14");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "Erreur cURL : " . curl_error($ch);
} else {
    echo "Réponse API : " . $response;
}
curl_close($ch);
?>
