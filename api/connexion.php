<?php
/**
 * API : Connexion utilisateur
 * Méthode : POST
 * Corps JSON attendu : { email, mot_de_passe }
 *
 * Remarque : conformément aux consignes, la "session" côté client est
 * gérée via sessionStorage en JavaScript. Cette API renvoie donc les
 * informations nécessaires (id, nom, prénom, photo, rôle) que le
 * frontend stockera lui-même dans sessionStorage après une connexion réussie.
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    repondreJSON(false, 'Méthode non autorisée.', [], 405);
}

$donnees = recupererCorpsJSON();

$manquants = champsManquants($donnees, ['email', 'mot_de_passe']);
if (!empty($manquants)) {
    repondreJSON(false, 'Champs manquants : ' . implode(', ', $manquants), [], 400);
}

$email      = trim(strtolower($donnees['email']));
$motDePasse = $donnees['mot_de_passe'];

try {
    $pdo = getConnexionDB();

    $stmt = $pdo->prepare('
        SELECT id, nom, prenom, email, mot_de_passe, photo_profil, role, email_verifie, statut
        FROM users
        WHERE email = :email
    ');
    $stmt->execute(['email' => $email]);
    $utilisateur = $stmt->fetch();

    // Message volontairement générique pour ne pas révéler si l'email existe
    if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
        repondreJSON(false, 'Email ou mot de passe incorrect.', [], 401);
    }

    if ((int)$utilisateur['email_verifie'] === 0) {
        repondreJSON(false, 'Veuillez confirmer votre adresse email avant de vous connecter.', [], 403);
    }

    if ($utilisateur['statut'] === 'suspendu') {
        repondreJSON(false, 'Votre compte a été suspendu. Contactez l’administrateur.', [], 403);
    }

    // ---- Mise à jour de la dernière connexion ----
    $stmt = $pdo->prepare('UPDATE users SET derniere_connexion = NOW() WHERE id = :id');
    $stmt->execute(['id' => $utilisateur['id']]);

    // ---- Données renvoyées au frontend pour sessionStorage ----
    unset($utilisateur['mot_de_passe']);

    repondreJSON(true, 'Connexion réussie.', ['utilisateur' => $utilisateur], 200);

} catch (PDOException $e) {
    error_log('Erreur connexion : ' . $e->getMessage());
    repondreJSON(false, 'Une erreur est survenue lors de la connexion.', [], 500);
}
