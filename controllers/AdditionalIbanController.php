<?php
class AdditionalIbanController
{
    public function __construct() {}
    public function displayAdditionalIban(): void
    {
        require "./vue/additionalIban.php";
    }

    public function displayCreateForm()
    {
        require "./vue/createAdditionalIban.php";
    }
    public function displayUpdateForm()
    {
        require "./vue/updateAdditionalIban.php";
    }
    public function delete()
    {
        $additionalIban = new additionalibans();
        $succes = $additionalIban->delete($_GET["id"]);
        header("location:index.php?pageController=additionalIban&action=display&ownerId=".$_GET["ownerId"]);
    }
}


