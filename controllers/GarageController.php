<?php
class GarageController
{
    public function __construct() {}
    public function displayGarage(): void {
        $pageName = "Liste des garages";
        require "./vue/garage.php";
    }
}
