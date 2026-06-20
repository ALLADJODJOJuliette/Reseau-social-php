<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$nom = $data['nom'] ?? null;
$prenom = $data['prenom'] ?? null;
$email = $data['email'] ?? null;
$telephone = $data['telephone'] ?? null;
$date_naissance = $data['date_naissance'] ?? null;
$sexe = $data['sexe'] ?? null;

if (!$user_id) {
    echo json_encode(["erreur" => "user_id manquant"]);
    exit;
}

$requete = $pdo->prepare("UPDATE users SET nom=?, prenom=?, email=?, telephone=?, date_naissance=?, sexe=? WHERE id=?");
$requete->execute([$nom, $prenom, $email, $telephone, $date_naissance, $sexe, $user_id]);

echo json_encode(["success" => true, "message" => "Profil mis à jour"]);
?>