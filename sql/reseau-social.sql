-- ============================================================
-- Schéma de base de données — Réseau Social Web (PHP/AJAX)
-- Base commune à toute l'équipe
-- ============================================================

CREATE DATABASE IF NOT EXISTS reseau_social
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reseau_social;

-- ------------------------------------------------------------
-- Table : users
-- Regroupe TOUT le monde (user, moderateur, administrateur)
-- La différence de droits se fait uniquement via la colonne "role"
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom                 VARCHAR(50)  NOT NULL,
    prenom              VARCHAR(50)  NOT NULL,
    email               VARCHAR(150) NOT NULL UNIQUE,
    telephone           VARCHAR(20)  DEFAULT NULL,
    mot_de_passe        VARCHAR(255) NOT NULL,            -- hash (password_hash)
    photo_profil        VARCHAR(255) DEFAULT 'assets/images/default-avatar.png',
    date_naissance      DATE         DEFAULT NULL,
    sexe                ENUM('homme','femme') DEFAULT NULL,
    bio                 VARCHAR(255) DEFAULT NULL,
    role                ENUM('user','moderateur','administrateur') NOT NULL DEFAULT 'user',
    email_verifie       TINYINT(1)   NOT NULL DEFAULT 0,
    statut              ENUM('actif','suspendu') NOT NULL DEFAULT 'actif',
    date_creation       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion  DATETIME NULL
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
        FOREIGN KEY (utilisateur_id) REFERENCES users(id)
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
        FOREIGN KEY (utilisateur_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : posts — les articles publiés par les users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    contenu TEXT NOT NULL,
    image VARCHAR(255),
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : likes — un user ne peut liker qu'une seule fois le même post
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    type_reaction ENUM('jaime','coeur','rire') NOT NULL,
    date_like DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (user_id, post_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : comments — commentaires sous un post
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : friendships — demandes d'amitié entre deux users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS friendships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    friend_id INT NOT NULL,
    statut ENUM('en_attente','accepte','refuse') DEFAULT 'en_attente',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : messages — chat privé entre deux users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    contenu TEXT,
    image VARCHAR(255),
    lu TINYINT(1) DEFAULT 0,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Données de test (identifiants pour le README / la démo)
-- Mot de passe en clair pour les tests : "Test1234!"
--
-- IMPORTANT : générez d'abord le hash bcrypt avec :
--   php -r "echo password_hash('Test1234!', PASSWORD_BCRYPT);"
-- puis collez-le dans les INSERT ci-dessous.
-- ------------------------------------------------------------
INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Principal', 'admin@reseausocial.com', '<<HASH_ICI>>', 'administrateur'),
('Modo', 'Principal', 'moderateur@reseausocial.com', '<<HASH_ICI>>', 'moderateur'),
('Client', 'Test', 'client@reseausocial.com', '<<HASH_ICI>>', 'user');