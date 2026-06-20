<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$ancien = $data['ancien_mot_de_passe'] ?? null;
$nouveau = $data['nouveau_mot_de_passe'] ?? null;

if (!$user_id || !$ancien || !$nouveau) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

// Vérifier l'ancien mot de passe
$requete = $pdo->prepare("SELECT mot_de_passe FROM users WHERE id = ?");
$requete->execute([$user_id]);
$user = $requete->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($ancien, $user['mot_de_passe'])) {
    echo json_encode(["success" => false, "message" => "Ancien mot de passe incorrect"]);
    exit;
}

// Mettre à jour le nouveau mot de passe
$nouveau_hash = password_hash($nouveau, PASSWORD_DEFAULT);
$requete = $pdo->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ?");
$requete->execute([$nouveau_hash, $user_id]);

echo json_encode(["success" => true, "message" => "Mot de passe modifié"]);
?>