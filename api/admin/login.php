<?php
// login.php
header('Content-Type: application/json');
$pdo=new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root', '');
//Récupération des données envoyées par JS
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'];
$motDePasse = $data['motDePasse'];
// Recherche de l'admin dans la base de données
$requete= $pdo->prepare('SELECT * FROM users WHERE email = :email');
$requete->execute(['email' => $email]);
$user = $requete->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($motDePasse, $user['mot_de_passe']) && $user['role'] !== 'user') {
    echo json_encode([
        'success' => true,
         'role' => $user['role'], 
         'id' => $user['id']]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Mot de passe ou email incorrect']);
}
?>