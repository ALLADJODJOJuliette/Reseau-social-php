-- ============================================================
-- Schéma de base de données — Réseau Social Web (PHP/AJAX)
-- ============================================================

CREATE DATABASE IF NOT EXISTS reseau_social
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reseau_social;

-- ------------------------------------------------------------
-- Table : users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom                 VARCHAR(50)  NOT NULL,
    prenom              VARCHAR(50)  NOT NULL,
    email               VARCHAR(150) NOT NULL UNIQUE,
    telephone           VARCHAR(20)  DEFAULT NULL,
    mot_de_passe        VARCHAR(255) NOT NULL,
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
-- Table : tokens_confirmation
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
-- Table : tokens_reset_password
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
-- Table : posts
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED NOT NULL,
    contenu          TEXT NOT NULL,
    image            VARCHAR(255) DEFAULT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : likes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS likes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    post_id       INT UNSIGNED NOT NULL,
    type_reaction ENUM('jaime','coeur','rire') NOT NULL,
    date_like     DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_likes_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_likes_post
        FOREIGN KEY (post_id) REFERENCES posts(id)
        ON DELETE CASCADE,
    UNIQUE KEY unique_like (user_id, post_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : comments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comments (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED NOT NULL,
    post_id          INT UNSIGNED NOT NULL,
    contenu          TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_comments_post
        FOREIGN KEY (post_id) REFERENCES posts(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : friendships
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS friendships (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    friend_id    INT UNSIGNED NOT NULL,
    statut       ENUM('en_attente','accepte','refuse') DEFAULT 'en_attente',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_friendships_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_friendships_friend
        FOREIGN KEY (friend_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : messages
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediteur_id   INT UNSIGNED NOT NULL,
    destinataire_id INT UNSIGNED NOT NULL,
    contenu         TEXT,
    image           VARCHAR(255) DEFAULT NULL,
    lu              TINYINT(1)   DEFAULT 0,
    date_envoi      DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_expediteur
        FOREIGN KEY (expediteur_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_messages_destinataire
        FOREIGN KEY (destinataire_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Données de test
-- Mot de passe en clair : "Test1234!"
-- Générer le hash avec :
--   php -r "echo password_hash('Test1234!', PASSWORD_BCRYPT);"
-- puis remplacer <<HASH_ICI>>
-- ------------------------------------------------------------
INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES
('Admin',  'Principal', 'admin@reseausocial.com',      '$2y$10$BMVAS6OWpvUKiP1DjypHluFphkM/TRbIik5Jcu.FD3sFordWYvdl.', 'administrateur'),
('Modo',   'Principal', 'moderateur@reseausocial.com', '$2y$10$BMVAS6OWpvUKiP1DjypHluFphkM/TRbIik5Jcu.FD3sFordWYvdl.', 'moderateur'),
('Client', 'Test',      'client@reseausocial.com',     '$2y$10$BMVAS6OWpvUKiP1DjypHluFphkM/TRbIik5Jcu.FD3sFordWYvdl.', 'user');