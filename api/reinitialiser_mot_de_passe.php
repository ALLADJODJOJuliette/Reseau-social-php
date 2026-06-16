<?php
/**
 * API : Réinitialisation du mot de passe (étape 2/2)
 * Méthode : POST
 * Corps JSON attendu : { token, nouveau_mot_de_passe }
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    repondreJSON(false, 'Méthode non autorisée.', [], 405);
}

$donnees = recupererCorpsJSON();

$manquants = champsManquants($donnees, ['token', 'nouveau_mot_de_passe']);
if (!empty($manquants)) {
    repondreJSON(false, 'Champs manquants : ' . implode(', ', $manquants), [], 400);
}

$token              = $donnees['token'];
$nouveauMotDePasse  = $donnees['nouveau_mot_de_passe'];

if (!motDePasseValide($nouveauMotDePasse)) {
    repondreJSON(false, 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.', [], 400);
}

try {
    $pdo = getConnexionDB();

    $stmt = $pdo->prepare('
        SELECT id AS token_id, utilisateur_id, date_expiration, utilise
        FROM tokens_reset_password
        WHERE token = :token
    ');
    $stmt->execute(['token' => $token]);
    $tokenLigne = $stmt->fetch();

    if (!$tokenLigne) {
        repondreJSON(false, 'Lien de réinitialisation invalide.', [], 400);
    }

    if ((int)$tokenLigne['utilise'] === 1) {
        repondreJSON(false, 'Ce lien de réinitialisation a déjà été utilisé.', [], 400);
    }

    if (strtotime($tokenLigne['date_expiration']) < time()) {
        repondreJSON(false, 'Ce lien de réinitialisation a expiré. Veuillez refaire une demande.', [], 400);
    }

    // ---- Mise à jour du mot de passe ----
    $pdo->beginTransaction();

    $hashMotDePasse = password_hash($nouveauMotDePasse, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = :mdp WHERE id = :id');
    $stmt->execute([
        'mdp' => $hashMotDePasse,
        'id'  => $tokenLigne['utilisateur_id'],
    ]);

    $stmt = $pdo->prepare('UPDATE tokens_reset_password SET utilise = 1 WHERE id = :id');
    $stmt->execute(['id' => $tokenLigne['token_id']]);

    $pdo->commit();

    repondreJSON(true, 'Votre mot de passe a été réinitialisé avec succès.', [], 200);

} catch (PDOException $e) {
    error_log('Erreur réinitialisation mot de passe : ' . $e->getMessage());
    repondreJSON(false, 'Une erreur est survenue.', [], 500);
}
