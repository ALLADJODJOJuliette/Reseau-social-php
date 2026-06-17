<?php
header('Content-Type: application/json');
$pdo=new PDO('mysql:host=localhost;port=3307;dbname=reseau_social;charset=utf8', 'root','');
$nbUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$nbPosts = $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$nbMessages = $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
echo json_encode([
    'nbUsers' => $nbUsers,
    'nbPosts' => $nbPosts,
    'nbMessages' => $nbMessages
]);
?>