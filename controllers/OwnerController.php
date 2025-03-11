<?php
class OwnerController
{
    public function __construct() {}
    public function displayOwner(): void {
        require "./vue/owner.php";
    }
    public function displayCreateForm(){
        require "./vue/createOwner.php";
    }
}
