<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "./vendor/autoload.php";

use Faker\Extension\Helper;
use PHPMailer\PHPMailer\PHPMailer;

require "./vue/header.php";
require "./Models/Database.php";
require "./Models/Users.php";
require "./Models/Owners.php";
require "./Models/Garages.php";
require "./Models/Tenants.php";
require "./Models/Leases.php";
require "./Models/AdditionalIbans.php";
require "./Models/Payments.php";
require "./Models/PaymentHistories.php";
require "./Models/EmailTemplates.php";
require "./Controllers/PageController.php";
require "./Controllers/UserController.php";
require "./Controllers/GarageController.php";
require "./Controllers/OwnerController.php";
require "./Controllers/TenantController.php";
require "./Controllers/LeasesController.php";
require "./Controllers/AdditionalIbanController.php";
require "./Controllers/PaymentController.php";
require "./Controllers/PaymentHistoryController.php";
require "./Controllers/EmailTemplateController.php";
require "./Services/MailService.php";
require "./Services/SendLeaseService.php";
if (!empty($_SESSION["role"])) {
    if ($_SESSION["role"] == "admin") {
        require "./vue/adminMenu.php";
    } else {
        require "./vue/ownerMenu.php";
    }
}
if (isset($_GET["pageController"])) {
    switch ($_GET["pageController"]) {
        case "":
            $user = new UserController();
            $user->displaySignInForm();
            break;
        case "user":
            $user = new UserController();
            if (empty($_GET["action"])) {
                $page = new PageController();
                $page->displayPageNotFound();
            } elseif ($_GET["action"] == "signIn" && empty($_SESSION)) {
                $user->displaySignInForm();
            } elseif ($_GET["action"] == "signOut") {
                $user->signOut();
            } elseif ($_GET["action"] == "profil") {
                $user->displayProfil();
            } elseif ($_GET["action"] == "requestresetpassword") {
                $user->displayRequestResetPassword();
            } elseif ($_GET["action"] == "resetpassword") {
                $user->displayResetPassword();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;
        case "owner":
            $owner = new OwnerController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && $_SESSION["role"] == "admin") {
                $owner->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "admin") {
                $owner->displayOwner();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && $_SESSION["role"] == "admin" && empty($_GET["id"])) {
                $owner->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $owner->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $owner->delete();
            } elseif ($_GET["action"] == "help" && !empty($_SESSION["adminId"]) && empty($_SESSION["ownerId"])) {
                $owner->startToHelp();
            } elseif ($_GET["action"] == "stophelp" && $_SESSION["role"] == "helper") {
                $owner->stopToHelp();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;
        case "garage":
            $garage = new GarageController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $garage->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $garage->displayGarage();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper") && empty($_GET["id"])) {
                $garage->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $garage->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $garage->delete();
            } elseif ($_GET["action"] == "duplicate") {
                $garage->displayDuplicateForm();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;
        case "tenant":
            $tenant = new TenantsController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $tenant->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $tenant->displayTenant();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper") && empty($_GET["id"])) {
                $tenant->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $tenant->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $tenant->delete();
            } elseif ($_GET["action"] == "tenantform") {
                $tenant->displayTenantForm();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;
        case "lease":
            $lease = new LeaseController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $lease->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $lease->displayLease();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper") && empty($_GET["id"])) {
                $lease->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $lease->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $lease->delete();
            } elseif ($_GET["action"] == "generate") {
                $lease->generate();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;
        case "additionalIban":
            $additionalIban = new AdditionalIbanController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $additionalIban->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $additionalIban->displayAdditionalIban();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper") && empty($_GET["id"])) {
                $additionalIban->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $additionalIban->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $additionalIban->delete();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;
        case "payment":
            $payment = new PaymentController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $payment->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $payment->displayTenant();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper") && empty($_GET["id"])) {
                $payment->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $payment->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $payment->delete();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;
        case "paymenthistory":
            $paymentHistory = new paymentHistoryController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $paymentHistory->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper")) {
                $paymentHistory->displayTenant();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && ($_SESSION["role"] == "owner" || $_SESSION["role"] == "helper") && empty($_GET["id"])) {
                $paymentHistory->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $paymentHistory->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $paymentHistory->delete();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;

        case "emailtemplate":
            $emailTemplate = new emailTemplateController();
            if ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "admin") {
                $emailTemplate->displayEmailTemplate();
            } elseif ($_GET["action"] == "update") {
                $emailTemplate->displayUpdateForm();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;

        default:
            $page = new PageController();
            $page->displayPageNotFound();
            break;
    }
} else {
    if (empty($_SESSION)) {
        $user = new UserController();
        $user->displaySignInForm();
    } elseif ($_SESSION["role"] == "owner") {
        $garage = new GarageController();
        $garage->displayGarage();
    } else {
        $user = new UserController();
        $user->displaySignInForm();
    }
}
require "./vue/footer.php";
