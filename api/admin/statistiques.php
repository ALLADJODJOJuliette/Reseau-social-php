<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : seul un admin/modérateur connecté peut voir les statistiques
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['administrateur', 'moderateur'])) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

// Statistiques de base
$nbUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$nbPosts = $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$nbMessages = $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$nbComments = $pdo->query('SELECT COUNT(*) FROM comments')->fetchColumn();
$nbLikes = $pdo->query('SELECT COUNT(*) FROM likes')->fetchColumn();

// Répartition par rôle
$nbAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'administrateur'")->fetchColumn();
$nbModerateurs = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'moderateur'")->fetchColumn();
$nbUsersSimples = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

// Comptes suspendus
$nbSuspendus = $pdo->query("SELECT COUNT(*) FROM users WHERE statut = 'suspendu'")->fetchColumn();

// Inscriptions des 7 derniers jours
$nbInscriptionsSemaine = $pdo->query("SELECT COUNT(*) FROM users WHERE date_creation >= NOW() - INTERVAL 7 DAY")->fetchColumn();

// Demandes d'amitié en attente
$nbDemandesAttente = $pdo->query("SELECT COUNT(*) FROM friendships WHERE statut = 'en_attente'")->fetchColumn();

echo json_encode([
    'nbUsers' => $nbUsers,
    'nbPosts' => $nbPosts,
    'nbMessages' => $nbMessages,
    'nbComments' => $nbComments,
    'nbLikes' => $nbLikes,
    'nbAdmins' => $nbAdmins,
    'nbModerateurs' => $nbModerateurs,
    'nbUsersSimples' => $nbUsersSimples,
    'nbSuspendus' => $nbSuspendus,
    'nbInscriptionsSemaine' => $nbInscriptionsSemaine,
    'nbDemandesAttente' => $nbDemandesAttente
]);
?>