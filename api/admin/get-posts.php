<?php
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$requete = $pdo->query('
    SELECT posts.id, posts.contenu, posts.image, posts.date_publication, users.nom, users.prenom
    FROM posts
    INNER JOIN users ON posts.user_id = users.id
    ORDER BY posts.id DESC
');
$posts = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($posts);
?>