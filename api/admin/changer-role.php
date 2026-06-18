<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$donnees = json_decode(file_get_contents('php://input'), true);
$id = $donnees['id'];
$nouveauRole = $donnees['role'];
$tokenRecu = $donnees['csrfToken'] ?? '';

// Vérification CSRF
if (!isset($_SESSION['csrf_token']) || $tokenRecu !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide (token CSRF incorrect)']);
    exit;
}
if(($_SESSION['admin_role'] ?? '') !== 'administrateur') {
    echo json_encode(['success' => false, 'message' => 'Accès refusé: rôle insuffisant']);
    exit;
}
$rolesAutorises = ['user', 'moderateur', 'administrateur'];
if (!in_array($nouveauRole, $rolesAutorises)) {
    echo json_encode(['success' => false, 'message' => 'Rôle invalide']);
    exit;
}

$requete = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
$requete->execute([$nouveauRole, $id]);

echo json_encode(['success' => true]);
?>