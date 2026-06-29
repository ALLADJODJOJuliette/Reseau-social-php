<?php
// api/posts/create_post.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$userId = $_POST['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

$contenu = trim($_POST['contenu'] ?? '');
$image_path = null;

if (empty($contenu) && empty($_FILES['image']['name'])) {
    echo json_encode(['success' => false, 'message' => 'Le post ne peut pas être vide']);
    exit;
}

if (!empty($_FILES['image']['name'])) {
    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $extensions_autorisees)) {
        echo json_encode(['success' => false, 'message' => 'Format image non autorisé']);
        exit;
    }

    $dossier = '../../assets/uploads/posts/';
    if (!is_dir($dossier)) mkdir($dossier, 0755, true);

    $nom_fichier = uniqid('post_', true) . '.' . $ext;
    $chemin_complet = $dossier . $nom_fichier;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $chemin_complet)) {
        echo json_encode(['success' => false, 'message' => 'Erreur upload image']);
        exit;
    }

    $image_path = 'assets/uploads/posts/' . $nom_fichier;
}

$host = 'localhost';
$db   = 'reseau_social';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, contenu, image)
        VALUES (:user_id, :contenu, :image)
    ");

    $stmt->execute([
        ':user_id' => $userId,
        ':contenu' => $contenu,
        ':image'   => $image_path
    ]);

    $post_id = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("
        SELECT p.id, p.contenu, p.image, p.date_publication,
               u.id AS auteur_id, u.nom, u.prenom, u.photo_profil
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = :id
    ");
    $stmt2->execute([':id' => $post_id]);
    $post = $stmt2->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'post' => $post]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>