<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['administrateur', 'moderateur'])) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé : vous n\'avez pas les permissions nécessaires pour accéder à cette ressource']);
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$requete = $pdo->query('SELECT id, nom, prenom, email, photo_profil, telephone, date_naissance,sexe,statut,date_inscription, role FROM users ORDER BY id DESC');
$users = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>