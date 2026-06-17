# Réseau Social Web — PHP & AJAX

Projet d'examen final : application web de réseau social inspirée de Facebook, développée en PHP natif (API), JavaScript natif (AJAX/Fetch) et MySQL.

> **Statut actuel** : le module **Authentification** (inscription, confirmation par email, connexion, mot de passe oublié) est complet et fonctionnel. Les modules Flux d'articles, Amis, Profil, Chat et Back-Office restent à développer.

## Description du projet

L'application permet à un utilisateur de créer un compte, de confirmer son adresse email, de se connecter et de réinitialiser son mot de passe en cas d'oubli — le tout sans rechargement de page, via des appels AJAX vers une API PHP.

## Architecture du projet

```
reseau-social/
├── index.html                  # Point d'entrée, redirige selon sessionStorage
├── composer.json                # Dépendance PHPMailer
├── assets/
│   ├── css/style.css            # Style global
│   ├── js/
│   │   ├── commun.js             # Helpers AJAX, sessionStorage
│   │   └── auth.js                # Logique inscription/connexion/reset
│   └── images/
├── vues/
│   ├── clients/
│   │   ├── connexion.html
│   │   ├── inscription.html
│   │   ├── mot-de-passe-oublie.html
│   │   ├── reinitialiser-mot-de-passe.html
│   │   └── accueil.html          # Placeholder post-connexion
│   └── back-office/               # À développer
├── api/
│   ├── inscription.php
│   ├── connexion.php
│   ├── confirmer_email.php
│   ├── mot_de_passe_oublie.php
│   ├── reinitialiser_mot_de_passe.php
│   └── utils/
│       ├── helpers.php
│       ├── mailer.php
│       └── generer_hash.php
├── config/
│   ├── database.php
│   └── config.php
└── sql/
    └── schema.sql
```

## Mode de fonctionnement

### 1. Installation

1. Créer la base de données : importer `sql/schema.sql` dans MySQL (via phpMyAdmin ou la ligne de commande `mysql -u root -p < sql/schema.sql`).
2. Adapter les identifiants de connexion à la base dans `config/database.php` (`DB_HOST`, `DB_USER`, `DB_PASS`).
3. Installer PHPMailer via Composer, à la racine du projet :
   ```
   composer require phpmailer/phpmailer
   ```
4. Configurer l'envoi d'email dans `config/config.php` (section SMTP) : renseigner `SMTP_USER` et `SMTP_PASS` avec un compte SMTP valide (ex : Gmail + mot de passe d'application).
5. Générer le hash du mot de passe administrateur de test et le coller dans `sql/schema.sql` avant import :
   ```
   php api/utils/generer_hash.php "Test1234!"
   ```
6. Servir le projet via un serveur PHP local (ex : `php -S localhost:8000` depuis la racine, ou XAMPP/WAMP).

### 2. Parcours utilisateur

- **Inscription** (`vues/clients/inscription.html`) : l'utilisateur renseigne nom, prénom, email et mot de passe. Le mot de passe est haché (bcrypt) côté serveur. Un email HTML de confirmation est envoyé avec un lien contenant un token valable 24h.
- **Confirmation email** (`api/confirmer_email.php`) : en cliquant sur le lien reçu, le compte est activé (`email_verifie = 1`).
- **Connexion** (`vues/clients/connexion.html`) : vérifie l'email/mot de passe ; refuse si l'email n'est pas confirmé. En cas de succès, les informations utilisateur sont stockées dans `sessionStorage` côté navigateur (équivalent JS des sessions PHP, comme exigé par les consignes).
- **Mot de passe oublié** (`vues/clients/mot-de-passe-oublie.html` puis `reinitialiser-mot-de-passe.html`) : un email HTML avec un lien de réinitialisation (token valable 1h) est envoyé ; le lien mène à un formulaire de saisie du nouveau mot de passe.

Toutes les interactions se font via `fetch()` (AJAX) vers les scripts PHP du dossier `api/`, sans aucun rechargement de page.

## Identifiants de test

| Rôle  | Email | Mot de passe |
|-------|-------|---------------|
| Administrateur (back-office) | admin@reseausocial.test | Test1234! |
| Client | À créer via le formulaire d'inscription | — |

> Remarque : pour tester le parcours client complet, créez un compte via `inscription.html`, puis confirmez l'email en cliquant sur le lien reçu (ou consultez le lien directement dans les logs SMTP si vous testez en local sans boîte mail réelle).

## Liste des membres du groupe

| Nom | Tâches réalisées |
|-----|--------------------|
| _À compléter_ | _À compléter_ |
| _À compléter_ | _À compléter_ |

## Sécurité implémentée

- Mots de passe hachés avec `password_hash()` (bcrypt), jamais stockés en clair.
- Requêtes SQL préparées (PDO) contre les injections SQL.
- Validation des entrées côté serveur (email, robustesse du mot de passe).
- Tokens de confirmation/réinitialisation aléatoires (`random_bytes`), à usage unique et avec expiration.
- Messages d'erreur génériques pour ne pas révéler l'existence d'un compte (mot de passe oublié, connexion).
