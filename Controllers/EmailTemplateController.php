<?php
class EmailTemplateController
{
    public function __construct() {}
    public function displayEmailTemplate(): void {
        require "./vue/emailTemplate.php";
    }
    public function displayUpdateForm(){
        require "./vue/updateEmailTemplate.php";
    }

}
