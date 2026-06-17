<?php
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost; dbname=reseau_social;charset=utf8', 'root', '');

$donnees = json_decode(file_get_contents('php://input'), true);
$id = $donnees['id'];

$requete = $pdo->prepare('DELETE FROM users WHERE id = ?');
$requete->execute([$id]);

echo json_encode(['success' => true]);
?>