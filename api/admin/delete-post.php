<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$donnees = json_decode(file_get_contents('php://input'), true);
$id = $donnees['id'];
$tokenRecu = $donnees['csrfToken'] ?? '';

// Vérification CSRF
if (!isset($_SESSION['csrf_token']) || $tokenRecu !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide (token CSRF incorrect)']);
    exit;
}

$requete = $pdo->prepare('DELETE FROM posts WHERE id = ?');
$requete->execute([$id]);

echo json_encode(['success' => true]);
?>