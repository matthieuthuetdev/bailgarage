<?php
// Paramètres de connexion à la base de données
$host = 'localhost:3306';
$dbname = 'bailgarage';
$username = 'Admin';
$password = 'G^jy3h922';

try {
    // Création de l'objet PDO pour la connexion à la base de données
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configuration de l'attribut pour afficher les erreurs
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
