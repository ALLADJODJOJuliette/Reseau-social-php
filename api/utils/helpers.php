<?php
/**
 * Fonctions utilitaires communes à toutes les API
 */

/**
 * Envoie une réponse JSON et termine le script.
 */
function repondreJSON(bool $succes, string $message, array $donnees = [], int $codeHttp = 200): void
{
    http_response_code($codeHttp);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(
        ['succes' => $succes, 'message' => $message],
        $donnees
    ));
    exit;
}

/**
 * Vérifie que les champs requis sont présents et non vides dans un tableau.
 *
 * @return string[] liste des champs manquants (vide si tout est OK)
 */
function champsManquants(array $donnees, array $champsRequis): array
{
    $manquants = [];
    foreach ($champsRequis as $champ) {
        if (!isset($donnees[$champ]) || trim((string)$donnees[$champ]) === '') {
            $manquants[] = $champ;
        }
    }
    return $manquants;
}

/**
 * Valide le format d'une adresse email.
 */
function emailValide(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide la robustesse d'un mot de passe :
 * au moins 8 caractères, une majuscule, une minuscule, un chiffre.
 */
function motDePasseValide(string $motDePasse): bool
{
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $motDePasse) === 1;
}

/**
 * Génère un token aléatoire sécurisé (utilisé pour la confirmation
 * d'email et la réinitialisation de mot de passe).
 */
function genererToken(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * Récupère le corps JSON de la requête sous forme de tableau associatif.
 */
function recupererCorpsJSON(): array
{
    $contenu = file_get_contents('php://input');
    $donnees = json_decode($contenu, true);
    return is_array($donnees) ? $donnees : [];
}
