<?php
//get-users.php
header('Content-Type: application/json');
// Vérification : seul un admin/moderateur peut accéder à cette page
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit;
        }
     session_start();
     if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['admin', 'moderateur'])) {
        echo json_encode(['success' => false, 'message' => 'Accès refusé']);
         exit;
     }
$pdo=new PDO('mysql:host=localhost;dbname=reseau_social;charset=utf8', 'root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$requete= $pdo->prepare('SELECT id, nom, prenom, email, telephone, date_de_naissance, sexe, statut, date_inscription ,role FROM users');
$requete->execute();
$users = $requete->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users);