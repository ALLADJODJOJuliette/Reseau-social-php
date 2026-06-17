-- ============================================================
-- Schéma de base de données — Réseau Social Web (PHP/AJAX)
-- Partie : Authentification
-- ============================================================

CREATE DATABASE IF NOT EXISTS reseau_social
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE reseau_social;

-- ------------------------------------------------------------
-- Table : utilisateurs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateurs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(50)  NOT NULL,
    prenom          VARCHAR(50)  NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255) NOT NULL,            -- hash (password_hash)
    photo_profil    VARCHAR(255) DEFAULT 'assets/images/default-avatar.png',
    date_naissance  DATE         DEFAULT NULL,
    bio              VARCHAR(255) DEFAULT NULL,
    role            ENUM('client') NOT NULL DEFAULT 'client',
    email_verifie   TINYINT(1)   NOT NULL DEFAULT 0,
    statut          ENUM('actif','suspendu') NOT NULL DEFAULT 'actif',
    date_creation   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : administrateurs (Back-Office : admin + modérateur)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS administrateurs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(50)  NOT NULL,
    prenom          VARCHAR(50)  NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255) NOT NULL,
    role            ENUM('administrateur','moderateur') NOT NULL DEFAULT 'moderateur',
    date_creation   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : tokens_confirmation (validation de l'email à l'inscription)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tokens_confirmation (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  INT UNSIGNED NOT NULL,
    token           VARCHAR(255) NOT NULL UNIQUE,
    date_expiration DATETIME     NOT NULL,
    utilise         TINYINT(1)   NOT NULL DEFAULT 0,
    CONSTRAINT fk_token_conf_user
        FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : tokens_reset_password (mot de passe oublié)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tokens_reset_password (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  INT UNSIGNED NOT NULL,
    token           VARCHAR(255) NOT NULL UNIQUE,
    date_expiration DATETIME     NOT NULL,
    utilise         TINYINT(1)   NOT NULL DEFAULT 0,
    CONSTRAINT fk_token_reset_user
        FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Données de test (identifiants pour le README / la démo)
-- Mot de passe en clair pour les tests : "Test1234!"
--
-- IMPORTANT : exécutez d'abord le script generer_hash.php (fourni dans
-- /api/utils/generer_hash.php) pour obtenir un hash bcrypt valide, puis
-- collez-le ci-dessous avant d'exécuter cet INSERT. Une commande en ligne :
--   php -r "echo password_hash('Test1234!', PASSWORD_BCRYPT);"
-- ------------------------------------------------------------
INSERT INTO administrateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Principal', 'admin@reseausocial.test',
 '<<COLLER_LE_HASH_GENERE_ICI>>', 'administrateur');
