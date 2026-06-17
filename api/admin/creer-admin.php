<?php
$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$motDePasseHash = password_hash('admin123', PASSWORD_DEFAULT);

$requete = $pdo->prepare('INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)');
$requete->execute(['Admin', 'Test', 'admin@test.com', $motDePasseHash, 'administrateur']);

echo "Admin créé avec succès !";
?>