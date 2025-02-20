<?php

/**
 * la classe UserController gère tout ce qui conserne les utilisateur du site, leurs connexion, leurs déconnexion, la création de propriétaire via la page d'administration.
 */
class UserController
{
    public function __construct() {}
    /**
     * cette fonction affiche le formulaire de connexion.
     */
    public function displaySignInForm(): void
    {
        require "./vue/signIn.php";
    }
    /**
     * cette fonction déconnecte l'utilisateur.
     */
    public function signOut(): void
    {
        session_destroy();
    }
}
