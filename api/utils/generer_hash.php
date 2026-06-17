<?php
/**
 * Script utilitaire (CLI uniquement) — génère un hash bcrypt
 * à coller dans sql/schema.sql pour créer le compte admin de test.
 *
 * Usage :
 *   php api/utils/generer_hash.php "Test1234!"
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Ce script ne peut être exécuté qu’en ligne de commande (CLI).');
}

$motDePasse = $argv[1] ?? 'Test1234!';
echo password_hash($motDePasse, PASSWORD_BCRYPT) . PHP_EOL;
