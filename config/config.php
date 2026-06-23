<?php
/**
 * Configuration générale de l'application
 */

// URL de base du site (à adapter selon l'environnement de déploiement)
define('URL_BASE', 'http://localhost/reseau-social-php');

// Configuration de l'expéditeur des emails
define('MAIL_EXPEDITEUR', 'no-reply@reseausocial.test');
define('MAIL_NOM_EXPEDITEUR', 'Réseau Social - Equipe Support');

// Configuration SMTP (utilisé par PHPMailer dans api/utils/mailer.php)
// Exemple avec Gmail : activez "mot de passe d'application" sur le compte Google
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
define('SMTP_PORT', 2525);
define('SMTP_USER', '72868e80b982f3');
define('SMTP_PASS', '211287c80e58d8');
define('SMTP_SECURE', 'tls'); // 'tls' ou 'ssl'

// Durée de validité des tokens (en heures)
define('DUREE_TOKEN_CONFIRMATION', 24);
define('DUREE_TOKEN_RESET', 1);
