<?php
require "./vue/header.php";
if (empty($_SESSION)) {
    require "./vue/menu.php";
} elseif ($_SESSION["right"] == "admin") {
    require "./vue/adminMenu.php";
}else {
    require "./vue/ownerMenu.php";
}
require "./models/Database.php";
require "./controllers/HomepageController.php";
if (isset($_GET["pagecontroller"])) {
    switch ($_GET["pagecontroller"]) {
        case '':
            $home = new HomepageController();
            $home->displayHome();
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
