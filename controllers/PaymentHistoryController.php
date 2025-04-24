<?php


class paymentHistoryController
{
    public function __construct() {}
    public function displayTenant(): void
    {
        require "./vue/paymentHistory.php";
    }
    public function displayCreateForm()
    {
        require "./vue/createPaymentHistory.php";
    }
    public function displayUpdateForm()
    {
        require "./vue/updatePaymentHistory.php";
    }
    public function delete()
    {
        $paymentHistories = new PaymentHistories();
        $succes = $paymentHistories->delete($_GET["id"], $_SESSION["ownerId"]);
        header("location:index.php?pageController=payment&action=display");
    }
}
