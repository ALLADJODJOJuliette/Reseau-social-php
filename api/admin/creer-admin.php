<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$donnees = json_decode(file_get_contents('php://input'), true);
$nom = $donnees['nom'] ?? '';
$prenom = $donnees['prenom'] ?? '';
$email = $donnees['email'] ?? '';
$motDePasse = $donnees['motDePasse'] ?? '';
$role = $donnees['role'] ?? '';
$tokenRecu = $donnees['csrfToken'] ?? '';

// Vérification CSRF
if (!isset($_SESSION['csrf_token']) || $tokenRecu !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide (token CSRF incorrect)']);
    exit;
}

// Seul un administrateur peut créer un compte admin/modérateur
if (($_SESSION['admin_role'] ?? '') !== 'administrateur') {
    echo json_encode(['success' => false, 'message' => 'Accès refusé : rôle insuffisant']);
    exit;
}

// Le rôle envoyé doit être un des deux rôles de back-office, jamais "user"
$rolesAutorises = ['moderateur', 'administrateur'];
if (!in_array($role, $rolesAutorises)) {
    echo json_encode(['success' => false, 'message' => 'Rôle invalide']);
    exit;
}

// Vérifications basiques des champs
if (empty($nom) || empty($prenom) || empty($email) || empty($motDePasse)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit;
}

// Vérifier que l'email n'existe pas déjà
$requeteVerif = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$requeteVerif->execute([$email]);
if ($requeteVerif->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
    exit;
}

$motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

$requete = $pdo->prepare('INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)');
$requete->execute([$nom, $prenom, $email, $motDePasseHash, $role]);

echo json_encode(['success' => true]);
?>