<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);

$friendship_id = $data['friendship_id'] ?? null;
$statut = $data['statut'] ?? null; // 'accepte' ou 'refuse'

if (!$friendship_id || !$statut) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

if (!in_array($statut, ['accepte', 'refuse'])) {
    echo json_encode(["success" => false, "message" => "Statut invalide"]);
    exit;
}

$requete = $pdo->prepare("UPDATE friendships SET statut = ? WHERE id = ?");
$requete->execute([$statut, $friendship_id]);

echo json_encode(["success" => true, "message" => "Demande " . $statut]);
?>