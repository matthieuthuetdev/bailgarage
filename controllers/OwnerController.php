<?php
class OwnerController
{
    public function __construct() {}
    public function displayOwner(): void {
        require "./vue/owner.php";
    }
}
