<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_GET['user_id'] ?? null;
$contact_id = $_GET['contact_id'] ?? null;

if (!$user_id || !$contact_id) {
    echo json_encode(["erreur" => "Paramètres manquants"]);
    exit;
}

$requete = $pdo->prepare("
    SELECT m.*, u.nom, u.prenom, u.photo_profil
    FROM messages m
    JOIN users u ON u.id = m.expediteur_id
    WHERE (m.expediteur_id = ? AND m.destinataire_id = ?)
    OR (m.expediteur_id = ? AND m.destinataire_id = ?)
    ORDER BY m.date_envoi ASC
");
$requete->execute([$user_id, $contact_id, $contact_id, $user_id]);
$messages = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>
