<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$pdo = new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_POST['user_id'] ?? null;

if (!$user_id || !isset($_FILES['photo'])) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

$photo = $_FILES['photo'];
$extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
$extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array(strtolower($extension), $extensions_autorisees)) {
    echo json_encode(["success" => false, "message" => "Format non autorisé"]);
    exit;
}

// Créer le dossier uploads/profils s'il n'existe pas
if (!is_dir('../../uploads/profils')) {
    mkdir('../../uploads/profils', 0777, true);
}

$nom_fichier = 'user_' . $user_id . '_' . time() . '.' . $extension;
$chemin = '../../uploads/profils/' . $nom_fichier;

if (move_uploaded_file($photo['tmp_name'], $chemin)) {
    $requete = $pdo->prepare("UPDATE users SET photo_profil = ? WHERE id = ?");
    $requete->execute([$nom_fichier, $user_id]);
    echo json_encode(["success" => true, "photo" => $nom_fichier]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur upload"]);
}
?>