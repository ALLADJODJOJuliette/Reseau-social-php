<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$donnees = json_decode(file_get_contents('php://input'), true);
$id = $donnees['id'];
$tokenRecu = $donnees['csrfToken'] ?? '';

// Vérification CSRF : le token reçu doit correspondre à celui en session
if (!isset($_SESSION['csrf_token']) || $tokenRecu !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide ']);
    exit;
}

// Récupérer le rôle de la personne qu'on veut supprimer
$monRole = $_SESSION['admin_role'] ?? '';
$requeteVerif = $pdo->prepare('SELECT role FROM users WHERE id = ?');
$requeteVerif->execute([$id]);
$cible = $requeteVerif->fetch(PDO::FETCH_ASSOC);

// Sécurité : seul un user normal peut être supprimé depuis cette page
if ($cible && $cible['role'] !== 'user' && $monRole !== 'administrateur') {
    echo json_encode(['success' => false, 'message' => 'Action non autorisée : utilisez la page Admins pour gérer ce compte']);
    exit;
}

$requete = $pdo->prepare('DELETE FROM users WHERE id = ?');
$requete->execute([$id]);

echo json_encode(['success' => true]);
?>