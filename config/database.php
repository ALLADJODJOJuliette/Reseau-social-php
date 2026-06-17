<?php
/**
 * Configuration et connexion à la base de données (PDO / MySQL)
 */

// ---- Paramètres de connexion (à adapter à votre environnement) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'reseau_social');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Retourne une instance PDO connectée à la base de données.
 * Utilise un singleton pour éviter d'ouvrir plusieurs connexions.
 */
function getConnexionDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode([
                'succes'  => false,
                'message' => 'Erreur de connexion à la base de données.',
            ]));
        }
    }

    return $pdo;
}
