<?php
include '../includes/db.php';

// Informations de l'utilisateur
$nom = 'admin';
$prenom = 'admin';
$email = 'admin@bailgarage.com';
$password = 'admin';
$role = 'admin';

// Hachage du mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertion de l'utilisateur dans la base de données
$sql = "INSERT INTO users (nom, prenom, password, email, role, created_at, updated_at) 
        VALUES (:nom, :prenom, :password, :email, :role, NOW(), NOW())";
$stmt = $db->prepare($sql);
$stmt->execute([
    'nom' => $nom,
    'prenom' => $prenom,
    'password' => $hashed_password,
    'email' => $email,
    'role' => $role
]);

echo "Utilisateur admin créé avec succès.";
?>
