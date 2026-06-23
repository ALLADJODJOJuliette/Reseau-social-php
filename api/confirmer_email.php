<?php
/**
 * API : Confirmation de l'adresse email
 * Méthode : GET
 * Paramètre : ?token=...
 *
 * Cette page est accédée directement via le lien envoyé par email,
 * elle affiche donc une page HTML simple plutôt qu'une réponse JSON.
 */

require_once __DIR__ . '/../config/database.php';

$token = $_GET['token'] ?? '';

function afficherPage(string $titre, string $message, bool $succes): void
{
    $couleur = $succes ? '#42b72a' : '#e7000b';
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($titre) . '</title>
        <style>
            body { font-family: Arial, sans-serif; background:#f0f2f5; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; }
            .carte { background:#fff; padding:40px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; max-width:420px; }
            h1 { color:' . $couleur . '; }
            a { color:#1877f2; text-decoration:none; font-weight:bold; }
        </style>
    </head>
    <body>
        <div class="carte">
            <h1>' . htmlspecialchars($titre) . '</h1>
            <p>' . htmlspecialchars($message) . '</p>
            <a href="../index.html">Retour à l’accueil</a>
        </div>
    </body>
    </html>';
    exit;
}

if (empty($token)) {
    afficherPage('Lien invalide', 'Aucun token fourni.', false);
}

try {
    $pdo = getConnexionDB();

    $stmt = $pdo->prepare('
        SELECT tc.id AS token_id, tc.utilisateur_id, tc.date_expiration, tc.utilise
        FROM tokens_confirmation tc
        WHERE tc.token = :token
    ');
    $stmt->execute(['token' => $token]);
    $tokenLigne = $stmt->fetch();

    if (!$tokenLigne) {
        afficherPage('Lien invalide', 'Ce lien de confirmation n’existe pas.', false);
    }

    if ((int)$tokenLigne['utilise'] === 1) {
        afficherPage('Déjà confirmé', 'Cette adresse email a déjà été confirmée.', true);
    }

    if (strtotime($tokenLigne['date_expiration']) < time()) {
        afficherPage('Lien expiré', 'Ce lien de confirmation a expiré. Veuillez vous reconnecter pour en recevoir un nouveau.', false);
    }

    // ---- Activation du compte ----
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('UPDATE users SET email_verifie = 1 WHERE id = :id');
    $stmt->execute(['id' => $tokenLigne['utilisateur_id']]);

    $stmt = $pdo->prepare('UPDATE tokens_confirmation SET utilise = 1 WHERE id = :id');
    $stmt->execute(['id' => $tokenLigne['token_id']]);

    $pdo->commit();

    afficherPage('Email confirmé !', 'Votre adresse email a été confirmée avec succès. Vous pouvez maintenant vous connecter.', true);

} catch (PDOException $e) {
    error_log('Erreur confirmation email : ' . $e->getMessage());
    afficherPage('Erreur', 'Une erreur est survenue. Veuillez réessayer plus tard.', false);
}
