<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "./vendor/autoload.php";
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
 require "./Controllers/PageController.php";
 require "./Controllers/UserController.php";
 require "./Controllers/GarageController.php";
 require "./Controllers/OwnerController.php";
 require "./Controllers/TenantController.php";
 require "./Controllers/LeasesController.php";
 require "./Controllers/AdditionalIbanController.php";
 require "./Controllers/PaymentController.php";
 require "./Controllers/PaymentHistoryController.php";
 require "./Services/MailService.php";
if (!empty($_SESSION) && $_SESSION["role"] == "admin") {
    require "./vue/adminMenu.php";
} elseif (!empty($_SESSION) && $_SESSION["role"] == "owner") {
    require "./vue/ownerMenu.php";
} else {
}


// rooter
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
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;

        case "garage":
            $garage = new GarageController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $garage->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $garage->displayGarage();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && $_SESSION["role"] == "owner" && empty($_GET["id"])) {
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
            if ($_GET["action"] == "create" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $tenant->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $tenant->displayTenant();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && $_SESSION["role"] == "owner" && empty($_GET["id"])) {
                $tenant->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $tenant->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $tenant->delete();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;


        case "lease":
            $lease = new LeaseController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $lease->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $lease->displayLease();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && $_SESSION["role"] == "owner" && empty($_GET["id"])) {
                $lease->displayCreateForm();
            } elseif ($_GET["action"] == "update") {
                $lease->displayUpdateForm();
            } elseif ($_GET["action"] == "delete") {
                $lease->delete();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;




        case "additionalIban":
            $additionalIban = new AdditionalIbanController();
            if ($_GET["action"] == "create" && !empty($_SESSION) && "additionalIban") {
                $additionalIban->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION)) {
                $additionalIban->displayAdditionalIban();
            } elseif (empty($_GET["action"]) && !empty($_SESSION)  && empty($_GET["id"])) {
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
            if ($_GET["action"] == "create" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $payment->displayCreateForm();
            } elseif ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                $payment->displayTenant();
            } elseif (empty($_GET["action"]) && !empty($_SESSION) && $_SESSION["role"] == "owner" && empty($_GET["id"])) {
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
                if ($_GET["action"] == "create" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                    $paymentHistory->displayCreateForm();
                } elseif ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                    $paymentHistory->displayTenant();
                } elseif (empty($_GET["action"]) && !empty($_SESSION) && $_SESSION["role"] == "owner" && empty($_GET["id"])) {
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
    


        default:
            $page = new PageController();
            $page->displayPageNotFound();
            break;
    }
} else {
    if (empty($_SESSION)) {
        $user = new UserController();
        $user->displaySignInForm();
    } elseif ($_SESSION["role"] == "prietaire") {
        $garage = new GarageController();
        $garage->displayGarage();
    } else {
        $user = new UserController();
        $user->displaySignInForm();
    }
}

require "./vue/footer.php";
