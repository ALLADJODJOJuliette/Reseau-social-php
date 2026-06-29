<?php
// api/likes/toggle_like.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$userId = $_POST['user_id'] ?? null;
$post_id = intval($_POST['post_id'] ?? 0);
$type_reaction = $_POST['type_reaction'] ?? 'jaime';
$reactions_valides = ['jaime', 'coeur', 'rire'];

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Post invalide']);
    exit;
}

if (!in_array($type_reaction, $reactions_valides)) {
    echo json_encode(['success' => false, 'message' => 'Réaction invalide']);
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
        SELECT id, type_reaction FROM likes
        WHERE user_id = :user_id AND post_id = :post_id
    ");
    $stmt->execute([':user_id' => $userId, ':post_id' => $post_id]);
    $like_existant = $stmt->fetch(PDO::FETCH_ASSOC);

    $action = '';

    if ($like_existant) {
        if ($like_existant['type_reaction'] === $type_reaction) {
            $stmt = $pdo->prepare("DELETE FROM likes WHERE id = :id");
            $stmt->execute([':id' => $like_existant['id']]);
            $action = 'supprime';
        } else {
            $stmt = $pdo->prepare("UPDATE likes SET type_reaction = :type_reaction WHERE id = :id");
            $stmt->execute([':type_reaction' => $type_reaction, ':id' => $like_existant['id']]);
            $action = 'modifie';
        }
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO likes (user_id, post_id, type_reaction)
            VALUES (:user_id, :post_id, :type_reaction)
        ");
        $stmt->execute([':user_id' => $userId, ':post_id' => $post_id, ':type_reaction' => $type_reaction]);
        $action = 'ajoute';
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id = :post_id");
    $stmt->execute([':post_id' => $post_id]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'success' => true,
        'action' => $action,
        'total_likes' => $total,
        'type_reaction' => $action === 'supprime' ? null : $type_reaction
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>