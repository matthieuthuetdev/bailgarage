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
    public function displayUpdateForm(){
        require "./vue/updateOwner.php";
    }
    public function delete(){
        $owner = new Owners();
        $succes = $owner->delete($_GET["id"]);
        header("location:index.php?pageController=owner&action")
    }

}
