<?php
class PageController
{
    public function __construct() {}
    public function displayPageNotFound(): void {
        require "./vue/notFound.php";
    }
}
