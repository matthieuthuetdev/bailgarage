<?php
class LeaseController
{
    public function __construct() {}
    public function displayLease(): void
    {
        require "./vue/lease.php";
    }
    public function displayCreateForm()
    {
        require "./vue/createLease.php";
    }
    public function displayUpdateForm()
    {
        require "./vue/updateLease.php";
    }
    public function delete()
    {
        $leases = new Leases();
        $succes = $leases->delete($_GET["id"], $_SESSION["ownerId"]);
        header("location:index.php?pageController=lease&action=display");
    }
    public function generate(){
        $sendLease = new SendLeaseService();
        $_SESSION["message"] = $sendLease->SendLeaseRequest($_GET["id"]);
        header("location:index.php?pageController=lease&action=display");
    }
}
