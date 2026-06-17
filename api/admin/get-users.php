<?php
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$requete = $pdo->query('SELECT id, nom, prenom, email, photo_profil, role, statut FROM users ORDER BY id DESC');
$users = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>