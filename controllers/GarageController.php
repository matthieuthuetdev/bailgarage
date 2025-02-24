<?php
class GarageController
{
    public function __construct() {}
    public function displayGarage(): void {
        require "./vue/garage.php";
    }
}
