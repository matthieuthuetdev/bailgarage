<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

require "./vue/header.php";
require "./models/Database.php";
require "./models/Users.php";
require "./models/Owners.php";
require "./models/Garages.php";
require "./models/Tenants.php";
require "./models/Leases.php";
require "./models/AdditionalIbans.php";
require "./models/Payments.php";
require "./controllers/PageController.php";
require "./controllers/UserController.php";
require "./controllers/GarageController.php";
require "./controllers/OwnerController.php";
require "./controllers/TenantController.php";
require "./controllers/LeasesController.php";
require "./controllers/AdditionalIbanController.php";
require "./controllers/PaymentController.php";
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
