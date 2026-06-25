<?php
// api/comments/get_comments.php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

$post_id = intval($_GET['post_id'] ?? 0);

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Post invalide']);
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
            c.id,
            c.contenu,
            c.date_commentaire,
            u.id AS auteur_id,
            u.nom,
            u.prenom,
            u.photo_profil
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = :post_id
        ORDER BY c.date_commentaire ASC
    ");

    $stmt->execute([':post_id' => $post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'comments' => $comments]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>