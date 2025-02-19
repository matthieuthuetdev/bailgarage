<?php
require "./vue/header.php";
if(!empty($_SESSION)){
    require "./vue/menu.php";
}
require "./models/Database.php";
require "./controllers/Homepage.php";
require "./models/Database.php";


require "./vue/footer.php";