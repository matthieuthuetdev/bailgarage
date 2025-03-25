<?php
mail("mattmatt.thuet@gmail.com", "test", "<h1>ceci est un test d'envois de mail en php ! </h1>");
require "./vue/header.php";
require "./models/Database.php";
require "./models/Users.php";
require "./models/Owners.php";
require "./models/Garages.php";
require "./models/Tenants.php";
require "./controllers/PageController.php";
require "./controllers/UserController.php";
require "./controllers/GarageController.php";
require "./controllers/OwnerController.php";
require "./controllers/tenantsController.php";
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
                $tenants = new TenantsController();
                if ($_GET["action"] == "create" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                    $tenants->displayCreateForm();
                } elseif ($_GET["action"] == "display" && !empty($_SESSION) && $_SESSION["role"] == "owner") {
                    $tenants->displayTenant();
                } elseif (empty($_GET["action"]) && !empty($_SESSION) && $_SESSION["role"] == "owner" && empty($_GET["id"])) {
                    $tenants->displayCreateForm();
                } elseif ($_GET["action"] == "update") {
                    $tenants->displayUpdateForm();
                } elseif ($_GET["action"] == "delete") {
                    $tenants->delete();
                } elseif ($_GET["action"] == "duplicate") {
                    $tenants->displayDuplicateForm();
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
