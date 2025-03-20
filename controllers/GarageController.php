<?php
class GarageController
{
    public function __construct() {}
    public function displayGarage(): void {
        require "./vue/garage.php";
    }
    public function displayCreateForm(){
        require "./vue/createGarage.php";
    }
    public function displayUpdateForm(){
        require "./vue/updateGarage.php";
    }
    public function delete(){
        $owner = new Garages();
        $succes = $owner->delete($_GET["id"]);
        header("location:index.php?pageController=garage&action=display");
    }

}
