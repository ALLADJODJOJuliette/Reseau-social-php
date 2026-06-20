<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["erreur" => "user_id manquant"]);
    exit;
}

$requete = $pdo->prepare("SELECT id, nom, prenom, email, telephone, date_naissance, sexe, photo_profil FROM users WHERE id = ?");
$requete->execute([$user_id]);
$user = $requete->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode($user);
} else {
    echo json_encode(["erreur" => "Utilisateur introuvable"]);
}
?>