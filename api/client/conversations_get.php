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
    SELECT DISTINCT
        u.id, u.nom, u.prenom, u.photo_profil,
        m.contenu as dernier_message,
        m.date_envoi
    FROM messages m
    JOIN users u ON u.id = IF(m.expediteur_id = ?, m.destinataire_id, m.expediteur_id)
    WHERE m.expediteur_id = ? OR m.destinataire_id = ?
    ORDER BY m.date_envoi DESC
");
$requete->execute([$user_id, $user_id, $user_id]);
$conversations = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($conversations);
?>