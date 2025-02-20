<?php
class HomepageController
{
    public function __construct() {}
    public function displayHome(): void {
        require "./vue/homepage.php";
    }
}
