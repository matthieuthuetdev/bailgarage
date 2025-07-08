<?php
class SendLeaseService
{
    public function __construct() {}
    public function SendLeaseRequest($leaseId)
    {
        $lease = new Leases();
        $lleaseInfo = $lease->read($_SESSION["ownerId"], $leaseId);
        $garage = new Garages();
        $garageInfo = $garage->read($_SESSION["ownerId"], $lleaseInfo["garageId"]);
        $tenant = new Tenants();
        $tenantInfo = $tenant->read($_SESSION["ownerId"], $lleaseInfo["tenantId"]);
        $owner = new Owners();
        $ownerInfo = $owner->read($_SESSION["ownerId"]);

        $data = [
            "template_id" => "e0379893-9c64-444e-8a25-ff4a84e77242",
            "signers" => [
                ["name" => (empty($tenantInfo["firstName"]) ? "" : $tenantInfo["firstName"]) . " " . (empty($tenantInfo["name"]) ? "" : $tenantInfo["name"]), "email" => empty($tenantInfo["email"]) ? "" : $tenantInfo["email"]],
                ["name" => (empty($ownerInfo["firstName"]) ? "" : $ownerInfo["firstName"]) . " " . (empty($ownerInfo["name"]) ? "" : $ownerInfo["name"]), "email" => empty($ownerInfo["email"]) ? "" : $ownerInfo["email"]]
            ],
            "placeholder_fields" => [
                ["api_key" => "ownerName",             "value" => empty($ownerInfo["name"]) ? "" : $ownerInfo["name"]],
                ["api_key" => "ownerFirstName",        "value" => empty($ownerInfo["firstName"]) ? "" : $ownerInfo["firstName"]],
                ["api_key" => "ownerAddress",          "value" => empty($ownerInfo["address"]) ? "" : $ownerInfo["address"]],
                ["api_key" => "ownerAdditionalAddress", "value" => empty($ownerInfo["additionalAddress"]) ? "" : $ownerInfo["additionalAddress"]],
                ["api_key" => "ownerPostalCode",       "value" => empty($ownerInfo["postalCode"]) ? "" : $ownerInfo["postalCode"]],
                ["api_key" => "ownerCityName",         "value" => empty($ownerInfo["cityName"]) ? "" : $ownerInfo["cityName"]],
                ["api_key" => "ownerCountry",          "value" => empty($ownerInfo["country"]) ? "" : $ownerInfo["country"]],
                ["api_key" => "ownerPhoneNumber",      "value" => empty($ownerInfo["phoneNumber"]) ? "" : $ownerInfo["phoneNumber"]],
                ["api_key" => "ownerEmail",            "value" => empty($ownerInfo["email"]) ? "" : $ownerInfo["email"]],
                ["api_key" => "ownerIban",             "value" => empty($ownerInfo["iban"]) ? "" : $ownerInfo["iban"]],
                ["api_key" => "ownerBic",              "value" => empty($ownerInfo["bic"]) ? "" : $ownerInfo["bic"]],
                ["api_key" => "tenantName",            "value" => empty($tenantInfo["name"]) ? "" : $tenantInfo["name"]],
                ["api_key" => "tenantFirstName",       "value" => empty($tenantInfo["firstName"]) ? "" : $tenantInfo["firstName"]],
                ["api_key" => "tenantAddress",         "value" => empty($tenantInfo["address"]) ? "" : $tenantInfo["address"]],
                ["api_key" => "tenantAdditionalAddress", "value" => empty($tenantInfo["additionalAddress"]) ? "" : $tenantInfo["additionalAddress"]],
                ["api_key" => "tenantPostalCode",      "value" => empty($tenantInfo["postalCode"]) ? "" : $tenantInfo["postalCode"]],
                ["api_key" => "tenantCityName",        "value" => empty($tenantInfo["cityName"]) ? "" : $tenantInfo["cityName"]],
                ["api_key" => "tenantCountry",         "value" => empty($tenantInfo["country"]) ? "" : $tenantInfo["country"]],
                ["api_key" => "tenantPhoneNumber",     "value" => empty($tenantInfo["phoneNumber"]) ? "" : $tenantInfo["phoneNumber"]],
                ["api_key" => "tenantEmail",           "value" => empty($tenantInfo["email"]) ? "" : $tenantInfo["email"]],
                ["api_key" => "garageAddress",         "value" => empty($garageInfo["address"]) ? "" : $garageInfo["address"]],
                ["api_key" => "garageAdditionalAddress", "value" => empty($garageInfo["additionalAddress"]) ? "" : $garageInfo["additionalAddress"]],
                ["api_key" => "garagePostalCode",      "value" => empty($garageInfo["postalCode"]) ? "" : $garageInfo["postalCode"]],
                ["api_key" => "garageCityName",        "value" => empty($garageInfo["cityName"]) ? "" : $garageInfo["cityName"]],
                ["api_key" => "garageCountry",         "value" => empty($garageInfo["country"]) ? "" : $garageInfo["country"]],
                ["api_key" => "garageNumber",          "value" => empty($garageInfo["garageNumber"]) ? "" : $garageInfo["garageNumber"]],
                ["api_key" => "garageSurface",         "value" => empty($garageInfo["surface"]) ? "" : $garageInfo["surface"]],
                ["api_key" => "garageNote",            "value" => empty($garageInfo["ownerNote"]) ? "" : $garageInfo["ownerNote"]],
                ["api_key" => "leaseStartDate",        "value" => empty($lleaseInfo["startDate"]) ? "" : $lleaseInfo["startDate"]],
                ["api_key" => "leaseDuration",         "value" => empty($lleaseInfo["duration"]) ? "" : $lleaseInfo["duration"]],
                ["api_key" => "leaseRentAmount",       "value" => empty($lleaseInfo["rentAmount"]) ? "" : $lleaseInfo["rentAmount"]],
                ["api_key" => "leaseRentAmountLetters", "value" => empty($lleaseInfo["rentAmountInLetter"]) ? "" : $lleaseInfo["rentAmountInLetter"]],
                ["api_key" => "leaseFirstPaymentAmount", "value" => empty($lleaseInfo["prorata"]) ? "" : $lleaseInfo["prorata"]],
                ["api_key" => "leaseCharges",          "value" => empty($lleaseInfo["chargesAmount"]) ? "" : $lleaseInfo["chargesAmount"]],
                ["api_key" => "leaseChargesLetters",   "value" => empty($lleaseInfo["chargesAmountInLetter"]) ? "" : $lleaseInfo["chargesAmountInLetter"]],
                ["api_key" => "leaseReference",        "value" => empty($lleaseInfo["reference"]) ? "" : $lleaseInfo["reference"]],
                ["api_key" => "leasesNumberOfKey",      "value" => empty($lleaseInfo["numberOfkey"]) ? "" : $lleaseInfo["numberOfkey"]],
                ["api_key" => "leasesNumberOfBeep",     "value" => empty($lleaseInfo["numberOfBeep"]) ? "" : $lleaseInfo["numberOfBeep"]],
                ["api_key" => "leasesMadeIn",          "value" => empty($lleaseInfo["madeIn"]) ? "" : $lleaseInfo["madeIn"]],
                ["api_key" => "leasesMadeThe",          "value" => empty($lleaseInfo["madeThe"]) ? "" : $lleaseInfo["madeThe"]],
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
            return "Erreur cURL : " . curl_error($ch);
        } else {
            return "Le bail a été générer et envoyer avec succès ! Réponse API : " . $response;
        }
        curl_close($ch);
    }
}
