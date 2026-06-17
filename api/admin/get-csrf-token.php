<?php 
header('Content-Type: application/json');
session_start();
// Générer un token CSRF unique pour la session si ce n'est pas déjà fait
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
echo json_encode(['token' => $_SESSION['csrf_token']]);
?>