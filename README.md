# Reseau-social-php
Projet final - Réseau social type Facebook - ESGIS

## Description du projet

Application web de type réseau social inspirée de Facebook, dévelopée en PHP natif (API) JavaScript natif (AJAX/Fetch) et MySQL. Le projet permet au utilisateurs de créer un compte, publier des articles, liker/commenter, gérer la liste des amis et chatter en temps quasi réel. Une interface de back-office permet aux admministrateur et modérateurs de gérer la plateforme.

## Technologie utilisées

-Frontend : HTML5, CSS3, JavaScript natif (Fecth API)
-Backend : PHP natif (API REST simple)
-Base de donnée : MySQL
-Emails : PHPMailer (template HTML)

## Architecture du projet

/
|-----index.html
|-----assets/
|  |----css/
|  |----images/
|  |----js/
|------vues/
|  |----clients/ --> Page d'accueil (accueil,profil,chat...)
|  |
|   -----back-office/   --> Pages administration
|--------api/          -->scripts PHP (authentification, posts,amis,chat,admin)
|
---------reseau-social-php.sql

## Mode de fonctionnement / Installation

1. Installer un serveur local. Dans notre cas c'est XAMP
2. Créer la base de données en important `reseau-social-pho.sql` via phpMyAdmin
3. Configurer la connexion à la base 
4. Configurer l'envoi d'emails dans 
5. Placer le dossier du projet dans `htdocs` (ou équivalent)
6. Lancer Apache + MySQL, puis ouvrir `http://localhost/nom-du-projet/index.html`

## Identifiants de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur |  |  |
| Modérateur | |  |
| Utilisateur (client) |  |  |

Connexion back-office : `vues/back-office/login.html`
Connexion client : `vues/clients/login.html`

## Membres du groupe et tâches réalisées

| Membre | Tâches réalisées |
|---|---|
| [KOUHOGBE Urielle] | Module d'authentification (inscription, connexion, mot de passe oublié, emails HTML) + structure de la base de données |
| [TOGBE Raissa] | Fil d'articles (publication, likes/dislikes, commentaires AJAX) + gestion des amis (invitations, profils) |
| [D'ALMEIDA Eliah] | Gestion du profil personnel + module Chat (conversations, recherche d'amis, envoi de messages/images) |
| [ALLADJODJO Juliette] | Back-office (admin/modérateur, dashboard, statistiques) + sécurité (CSRF, hachage, requêtes préparées) + tests + documentation + dépôt GitHub |

## Fonctionnalités implémentées

-  Inscription avec confirmation par email
- Connexion / mot de passe oublié
-  Fil d'articles avec likes et commentaires en AJAX
-  Gestion des amis (invitations, acceptation, refus)
- Modification du profil et du mot de passe
- Chat en temps quasi réel (texte + images)
-  Back-office avec rôles administrateur / modérateur
-  Dashboard avec statistiques

## Sécurité

- Mots de passe hachés avec `password_hash()` 
- Requêtes préparées 
- Échappement des entrées utilisateur 
- Tokens CSRF sur les actions sensibles du back-office
- Vérification du rôle côté serveur sur chaque requête sensible

## Lien du dépôt

https://github.com/ALLADJODJOJuliette/Reseau-social-php.git