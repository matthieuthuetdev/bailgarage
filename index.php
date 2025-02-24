<?php
require "./vue/header.php";
require "./models/Database.php";
require "./models/Users.php";
require "./controllers/HomepageController.php";
require "./controllers/UserController.php";
require "./controllers/GarageController.php";
if (empty($_SESSION)) {
    require "./vue/menu.php";
} elseif ($_SESSION["right"] == "admin") {
    require "./vue/adminMenu.php";
} else {
    require "./vue/ownerMenu.php";
}
if (isset($_GET["pageController"])) {
    switch ($_GET["pageController"]) {
        case "user":
            $user = new UserController();
            if (empty($_GET["action"])) {
                header("location:index.php");
            } elseif ($_GET["action"] == "signIn") {
                $user->displaySignInForm();
            }
            break;
        case "garage":
            $garage = new GarageController();
            if (empty($_GET["action"])) {
                header("location:index.php");
                
            } elseif ($_GET["action"] == "display") {
                $garage->displayGarage();
            }
            break;





        default:
            $home = new HomepageController();
            $home->displayHome();

            break;
    }
} else {
    $home = new HomepageController();
    $home->displayHome();
}

require "./vue/footer.php";
