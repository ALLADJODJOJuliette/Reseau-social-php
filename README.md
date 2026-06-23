# Réseau Social Web — PHP & AJAX

Projet final réalisé dans le cadre de l'examen de fin d'année, ESGIS — Licence 2 IRT/AL.

Application web de type réseau social, inspirée de Facebook, développée en PHP natif (API), JavaScript natif (AJAX, sans rechargement de page) et MySQL.

---

## Description du projet

Le projet comprend :

- **Module d'authentification** : inscription avec confirmation par email, connexion, mot de passe oublié
- **Flux d'articles** : publication, likes/dislikes, commentaires en AJAX
- **Gestion des amis** : invitations, acceptation/refus, consultation de profils
- **Profil personnel** : modification des informations, photo de profil, mot de passe
- **Module Chat** : conversations en temps réel (simulation par intervalle JS)
- **Back-office** : tableau de bord, gestion des utilisateurs, des articles, et des comptes administrateurs/modérateurs, avec sécurité CSRF et contrôle des rôles

---

## Mode de fonctionnement

### Architecture

```
reseau-social-php/
├── api/            → scripts PHP (API JSON)
│   └── admin/      → endpoints du back-office
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
├── config/         → connexion à la base de données
├── sql/            → script de création de la base
├── vues/
│   ├── clients/    → pages du site public
│   └── back-office/→ pages d'administration
└── index.html
```

### Fonctionnement général

- Toutes les interactions se font en **AJAX** (`fetch`), sans rechargement de page après le chargement initial.
- La navigation et l'état de connexion côté client sont gérés via **`sessionStorage`** en JavaScript.
- Les actions sensibles (suppression, changement de rôle) sont protégées côté serveur par des **sessions PHP** et un **jeton CSRF**, en complément de `sessionStorage`, pour empêcher toute falsification de rôle depuis le navigateur.
- La base de données utilise une **table `users` unique**, la distinction des droits (utilisateur / modérateur / administrateur) se faisant uniquement via la colonne `role`.

### Installation

1. Importer le script SQL situé dans sql/ via phpMyAdmin (onglet "Importer")
2. Générer les mots de passe de test avec :
   
   php -r "echo password_hash('Test1234!', PASSWORD_BCRYPT);"
   
   et les insérer dans la table users si ce n'est pas déjà fait
3. Lancer Apache et MySQL via XAMPP
4. Accéder au projet via `http://localhost/reseau-social-php/`

---

## Identifiants de test

### Espace client

| Email                   | Mot de passe |
| client@reseausocial.com | Test1234!    |

### Espace back-office (administration)

| Email                        | Mot de passe | Rôle           |
| admin@reseausocial.com       | Test1234!    | Administrateur |
| moderateur@reseausocial.com  | Test1234!    | Modérateur     |



## Membres du groupe

- ALLADJODJO Mahuna Juliette
- KOUHOGBE Urielle
- TOGBE Raissa
- D'ALMEIDA Eliah



## Répartition des tâches

**KOUHOGBE Urielle** — Authentification & Base de données
- Inscription avec confirmation par email (template HTML)
- Connexion et gestion de session côté client
- Mot de passe oublié (réinitialisation par email)
- Conception initiale de la base de données

**TOGBE Raissa** — Articles, commentaires, likes/dislikes
- Page d'accueil et flux d'articles
- Publication et affichage des articles
- Système de likes/dislikes avec persistance
- Commentaires en AJAX

**D'ALMEIDA Eliah** — Amis, profils et chat
- Gestion des demandes d'amitié (envoi, acceptation, refus)
- Consultation des profils utilisateurs
- Modification du profil personnel et du mot de passe
- Module de chat (conversations, envoi de messages et d'images)

**ALLADJODJO Mahuna Juliette** — Administration, sécurité, tests, documentation et GitHub
- Back-office : authentification admin/modérateur distincte
- Dashboard avec statistiques détaillées
- Gestion des utilisateurs (consultation, recherche de profil détaillé, suspension/suppression)
- Gestion des articles (consultation, suppression)
- Gestion des comptes administrateurs et modérateurs (promotion, création, suppression)
- Sécurité : protection CSRF sur toutes les actions sensibles, contrôle des rôles côté serveur, fusion et cohérence du schéma de base de données
- Tests fonctionnels de l'ensemble du back-office
- Gestion du dépôt GitHub et documentation

---

## Dépôt GitHub

[https://github.com/ALLADJODJOJuliette/Reseau-social-php](https://github.com/ALLADJODJOJuliette/Reseau-social-php)