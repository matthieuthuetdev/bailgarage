<?php

use Faker\Provider\ar_EG\Payment;

class paymentController
{
    public function __construct() {}
    public function displayTenant(): void
    {
        require "./vue/payment.php";
    }
    public function displayCreateForm()
    {
        require "./vue/createPayment.php";
    }
    public function displayUpdateForm()
    {
        require "./vue/updatePayment.php";
    }
    public function delete()
    {
        $payment = new Payments();
        $succes = $payment->delete($_GET["id"], $_SESSION["ownerId"]);
        header("location:index.php?pageController=payment&action=display");
    }
}
