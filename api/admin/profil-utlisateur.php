<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['administrateur', 'moderateur'])) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$recherche = $_GET['recherche'] ?? '';

// On cherche par nom, prénom ou email
$requete = $pdo->prepare('SELECT * FROM users WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? LIMIT 1');
$terme = '%' . $recherche . '%';
$requete->execute([$terme, $terme, $terme]);
$user = $requete->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Aucun utilisateur trouvé']);
    exit;
}

$id = $user['id'];

// Statistiques personnelles de cet utilisateur
$nbPosts = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE user_id = ?');
$nbPosts->execute([$id]);
$nbPosts = $nbPosts->fetchColumn();

$nbComments = $pdo->prepare('SELECT COUNT(*) FROM comments WHERE user_id = ?');
$nbComments->execute([$id]);
$nbComments = $nbComments->fetchColumn();

$nbLikesDonnes = $pdo->prepare('SELECT COUNT(*) FROM likes WHERE user_id = ?');
$nbLikesDonnes->execute([$id]);
$nbLikesDonnes = $nbLikesDonnes->fetchColumn();

// Répartition des types de réactions données par cet utilisateur
$typesLikes = $pdo->prepare('SELECT type_reaction, COUNT(*) as total FROM likes WHERE user_id = ? GROUP BY type_reaction');
$typesLikes->execute([$id]);
$typesLikes = $typesLikes->fetchAll(PDO::FETCH_ASSOC);

$nbMessagesEnvoyes = $pdo->prepare('SELECT COUNT(*) FROM messages WHERE expediteur_id = ?');
$nbMessagesEnvoyes->execute([$id]);
$nbMessagesEnvoyes = $nbMessagesEnvoyes->fetchColumn();

$nbMessagesRecus = $pdo->prepare('SELECT COUNT(*) FROM messages WHERE destinataire_id = ?');
$nbMessagesRecus->execute([$id]);
$nbMessagesRecus = $nbMessagesRecus->fetchColumn();

$nbAmis = $pdo->prepare("SELECT COUNT(*) FROM friendships WHERE (user_id = ? OR friend_id = ?) AND statut = 'accepte'");
$nbAmis->execute([$id, $id]);
$nbAmis = $nbAmis->fetchColumn();

echo json_encode([
    'success' => true,
    'nom' => $user['nom'],
    'prenom' => $user['prenom'],
    'email' => $user['email'],
    'role' => $user['role'],
    'statut' => $user['statut'],
    'date_creation' => $user['date_creation'],
    'nbPosts' => $nbPosts,
    'nbComments' => $nbComments,
    'nbLikesDonnes' => $nbLikesDonnes,
    'typesLikes' => $typesLikes,
    'nbMessagesEnvoyes' => $nbMessagesEnvoyes,
    'nbMessagesRecus' => $nbMessagesRecus,
    'nbAmis' => $nbAmis
]);
?>