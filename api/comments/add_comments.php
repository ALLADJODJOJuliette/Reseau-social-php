<?php
// api/comments/add_comment.php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$contenu = trim($_POST['contenu'] ?? '');

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Post invalide']);
    exit;
}

if (empty($contenu)) {
    echo json_encode(['success' => false, 'message' => 'Commentaire vide']);
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
        INSERT INTO comments (user_id, post_id, contenu)
        VALUES (:user_id, :post_id, :contenu)
    ");

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':post_id' => $post_id,
        ':contenu' => $contenu
    ]);

    $comment_id = $pdo->lastInsertId();

    // Récupérer le commentaire avec les infos auteur
    $stmt2 = $pdo->prepare("
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
        WHERE c.id = :id
    ");
    $stmt2->execute([':id' => $comment_id]);
    $comment = $stmt2->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'comment' => $comment]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>