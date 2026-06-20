<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$friend_id = $data['friend_id'] ?? null;

if (!$user_id || !$friend_id) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

// Vérifier si une demande existe déjà
$requete = $pdo->prepare("SELECT id FROM friendships WHERE user_id = ? AND friend_id = ?");
$requete->execute([$user_id, $friend_id]);

if ($requete->fetch()) {
    echo json_encode(["success" => false, "message" => "Demande déjà envoyée"]);
    exit;
}

$requete = $pdo->prepare("INSERT INTO friendships (user_id, friend_id, statut) VALUES (?, ?, 'en_attente')");
$requete->execute([$user_id, $friend_id]);

echo json_encode(["success" => true, "message" => "Demande envoyée"]);
?>