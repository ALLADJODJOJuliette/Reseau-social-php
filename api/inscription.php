<?php
/**
 * API : Inscription d'un nouvel utilisateur
 * Méthode : POST
 * Corps JSON attendu : { nom, prenom, email, mot_de_passe }
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
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

// ---- Validation des champs ----
$manquants = champsManquants($donnees, ['nom', 'prenom', 'email', 'mot_de_passe']);
if (!empty($manquants)) {
    repondreJSON(false, 'Champs manquants : ' . implode(', ', $manquants), [], 400);
}

$nom         = trim($donnees['nom']);
$prenom      = trim($donnees['prenom']);
$email       = trim(strtolower($donnees['email']));
$motDePasse  = $donnees['mot_de_passe'];

if (!emailValide($email)) {
    repondreJSON(false, 'Adresse email invalide.', [], 400);
}

if (!motDePasseValide($motDePasse)) {
    repondreJSON(false, 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.', [], 400);
}

try {
    $pdo = getConnexionDB();

    // ---- Vérifier que l'email n'est pas déjà utilisé ----
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        repondreJSON(false, 'Cette adresse email est déjà utilisée.', [], 409);
    }

    // ---- Insertion de l'utilisateur ----
    $hashMotDePasse = password_hash($motDePasse, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('
        INSERT INTO users (nom, prenom, email, mot_de_passe, email_verifie)
        VALUES (:nom, :prenom, :email, :mot_de_passe, 0)
    ');
    $stmt->execute([
        'nom'          => $nom,
        'prenom'       => $prenom,
        'email'        => $email,
        'mot_de_passe' => $hashMotDePasse,
    ]);

    $utilisateurId = (int)$pdo->lastInsertId();

    // ---- Génération du token de confirmation ----
    $token      = genererToken();
    $expiration = date('Y-m-d H:i:s', strtotime('+' . DUREE_TOKEN_CONFIRMATION . ' hours'));

    $stmt = $pdo->prepare('
        INSERT INTO tokens_confirmation (utilisateur_id, token, date_expiration)
        VALUES (:utilisateur_id, :token, :date_expiration)
    ');
    $stmt->execute([
        'utilisateur_id'  => $utilisateurId,
        'token'           => $token,
        'date_expiration' => $expiration,
    ]);

    // ---- Envoi de l'email de confirmation (HTML) ----
    $lienConfirmation = URL_BASE . '/api/confirmer_email.php?token=' . $token;
    $corpsHtml         = templateEmailConfirmation($prenom, $lienConfirmation);
    $emailEnvoye       = envoyerEmail($email, $prenom . ' ' . $nom, 'Confirmez votre adresse email', $corpsHtml);

    repondreJSON(true, 'Inscription réussie. Un email de confirmation vous a été envoyé.', [
        'email_envoye'    => $emailEnvoye,
        'utilisateur_id'  => $utilisateurId,
    ], 201);

} catch (PDOException $e) {
    error_log('Erreur inscription : ' . $e->getMessage());
    repondreJSON(false, 'Une erreur est survenue lors de l’inscription.', [], 500);
}
