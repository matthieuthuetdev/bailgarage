<?php
class TenantsController
{
    public function __construct() {}
    public function displayTenant(): void
    {
        require "./vue/tenant.php";
    }
    public function displayCreateForm()
    {
        require "./vue/createTenant.php";
    }
    public function displayUpdateForm()
    {
        require "./vue/updateTenant.php";
    }
    public function delete()
    {
        $tenants = new Tenants();
        $succes = $tenants->delete($_GET["id"], $_SESSION["ownerId"]);
        header("location:index.php?pageController=tenant&action=display");
    }
}
