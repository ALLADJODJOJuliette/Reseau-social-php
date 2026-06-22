<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$expediteur_id = $_POST['expediteur_id'] ?? null;
$destinataire_id = $_POST['destinataire_id'] ?? null;
$contenu = $_POST['contenu'] ?? null;

if (!$expediteur_id || !$destinataire_id) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

// Gestion image
$image = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array(strtolower($extension), $extensions_autorisees)) {
        if (!is_dir('../../uploads/messages')) {
            mkdir('../../uploads/messages', 0777, true);
        }
        $nom_fichier = 'msg_' . time() . '.' . $extension;
        $chemin = '../../uploads/messages/' . $nom_fichier;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $chemin)) {
            $image = $nom_fichier;
        }
    }
}

$requete = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, image) VALUES (?, ?, ?, ?)");
$requete->execute([$expediteur_id, $destinataire_id, $contenu, $image]);

echo json_encode(["success" => true, "message" => "Message envoyé"]);
?>