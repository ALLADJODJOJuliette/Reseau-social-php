<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["erreur" => "user_id manquant"]);
    exit;
}

$requete = $pdo->prepare("
    SELECT u.id, u.nom, u.prenom, u.photo_profil, f.id as friendship_id
    FROM users u
    JOIN friendships f 
        ON (f.user_id = ? AND f.friend_id = u.id)
        OR (f.friend_id = ? AND f.user_id = u.id)
    WHERE f.statut = 'accepte'
");
$requete->execute([$user_id, $user_id]);
$amis = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($amis);
?>