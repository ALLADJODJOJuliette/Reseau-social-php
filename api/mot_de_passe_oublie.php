<?php
/**
 * API : Demande de réinitialisation de mot de passe (étape 1/2)
 * Méthode : POST
 * Corps JSON attendu : { email }
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/utils/helpers.php';
require_once __DIR__ . '/utils/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    repondreJSON(false, 'Méthode non autorisée.', [], 405);
}

$donnees = recupererCorpsJSON();

$manquants = champsManquants($donnees, ['email']);
if (!empty($manquants)) {
    repondreJSON(false, 'Veuillez fournir une adresse email.', [], 400);
}

$email = trim(strtolower($donnees['email']));

if (!emailValide($email)) {
    repondreJSON(false, 'Adresse email invalide.', [], 400);
}

try {
    $pdo = getConnexionDB();

    $stmt = $pdo->prepare('SELECT id, nom, prenom FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $utilisateur = $stmt->fetch();

    // ---- Réponse volontairement identique que l'email existe ou non ----
    // (évite de révéler quels emails sont enregistrés dans le système)
    $messagePublic = 'Si cette adresse existe dans notre système, un email de réinitialisation a été envoyé.';

    if (!$utilisateur) {
        repondreJSON(true, $messagePublic, [], 200);
    }

    // ---- Génération du token de reset ----
    $token      = genererToken();
    $expiration = date('Y-m-d H:i:s', strtotime('+' . DUREE_TOKEN_RESET . ' hours'));

    $stmt = $pdo->prepare('
        INSERT INTO tokens_reset_password (utilisateur_id, token, date_expiration)
        VALUES (:utilisateur_id, :token, :date_expiration)
    ');
    $stmt->execute([
        'utilisateur_id'  => $utilisateur['id'],
        'token'           => $token,
        'date_expiration' => $expiration,
    ]);

    // ---- Envoi de l'email HTML ----
    $lienReset   = URL_BASE . '/vues/clients/reinitialiser-mot-de-passe.html?token=' . $token;
    $corpsHtml   = templateEmailResetPassword($utilisateur['prenom'], $lienReset);
    envoyerEmail($email, $utilisateur['prenom'] . ' ' . $utilisateur['nom'], 'Réinitialisation de votre mot de passe', $corpsHtml);

    repondreJSON(true, $messagePublic, [], 200);

} catch (PDOException $e) {
    error_log('Erreur mot de passe oublié : ' . $e->getMessage());
    repondreJSON(false, 'Une erreur est survenue.', [], 500);
}
