<?php
class OwnerController
{
    public function __construct() {}
    public function displayOwner(): void
    {
        require "./vue/owner.php";
    }
    public function displayCreateForm()
    {
        require "./vue/createOwner.php";
    }
    public function displayUpdateForm()
    {
        require "./vue/updateOwner.php";
    }
    public function delete()
    {
        $owner = new Owners();
        $succes = $owner->delete($_GET["id"]);
        header("location:index.php?pageController=owner&action=display");
    }
    public function startToHelp()
    {
        $_SESSION["ownerId"] = $_GET["id"];
        $_SESSION["role"] = "helper";
        header("location: index.php?pageController=garage&action=display");
    }
        public function stopToHelp()
    {
        $_SESSION["ownerId"] = null;
        $_SESSION["role"] = "admin";
        header("location: index.php?pageController=owner&action=display");
    }

}
