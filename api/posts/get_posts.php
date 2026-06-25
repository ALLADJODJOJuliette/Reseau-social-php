<?php
// api/posts/get_posts.php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

$host = 'localhost';
$db   = 'reseau_social';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.contenu,
            p.image,
            p.date_publication,
            u.id AS auteur_id,
            u.nom,
            u.prenom,
            u.photo_profil,
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS total_likes,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS total_comments,
            (SELECT type_reaction FROM likes l2 
             WHERE l2.post_id = p.id AND l2.user_id = :user_id) AS ma_reaction
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.date_publication DESC
    ");

    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'posts' => $posts]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>