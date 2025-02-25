<?php
require "./vue/header.php";
require "./models/Database.php";
require "./models/Users.php";
require "./controllers/PageController.php";
require "./controllers/UserController.php";
require "./controllers/GarageController.php";
require "./controllers/OwnerController.php";
if (!empty($_SESSION) && $_SESSION["right"] == "admin") {
    require "./vue/adminMenu.php";
} elseif (!empty($_SESSION) && $_SESSION["right"] == "proprietaire") {
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




        case "garage":
            $garage = new GarageController();
            if (empty($_GET["action"])) {
                $page = new PageController();
                $page->displayPageNotFound();
            } elseif ($_GET["action"] == "display") {
                $garage->displayGarage();
            } else {
                $page = new PageController();
                $page->displayPageNotFound();
            }
            break;



















        case "owner":
            $owner = new OwnerController();
            if (empty($_GET["action"])) {
                $page = new PageController();
                $page->displayPageNotFound();
            } elseif ($_GET["action"] == "display") {
                $owner->displayOwner();
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
    } elseif ($_SESSION["right"] == "prietaire") {
        $garage = new GarageController();
        $garage->displayGarage();
    } else {
        $user = new UserController();
        $user->displaySignInForm();
    }
}

require "./vue/footer.php";
