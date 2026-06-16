<?php
/**
 * Helper d'envoi d'emails (PHPMailer + SMTP)
 *
 * Installation requise (une seule fois, à la racine du projet) :
 *   composer require phpmailer/phpmailer
 *
 * Cela crée un dossier /vendor avec l'autoload nécessaire.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envoie un email HTML via SMTP (PHPMailer).
 *
 * @param string $destinataireEmail
 * @param string $destinataireNom
 * @param string $sujet
 * @param string $corpsHtml
 * @return bool true si l'envoi a réussi, false sinon
 */
function envoyerEmail(string $destinataireEmail, string $destinataireNom, string $sujet, string $corpsHtml): bool
{
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Expéditeur / destinataire
        $mail->setFrom(MAIL_EXPEDITEUR, MAIL_NOM_EXPEDITEUR);
        $mail->addAddress($destinataireEmail, $destinataireNom);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $corpsHtml;
        $mail->AltBody = strip_tags($corpsHtml);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi email : ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Génère le template HTML de l'email de confirmation d'inscription.
 */
function templateEmailConfirmation(string $prenom, string $lienConfirmation): string
{
    return '
    <!DOCTYPE html>
    <html lang="fr">
    <head><meta charset="UTF-8"></head>
    <body style="margin:0;padding:0;background-color:#f0f2f5;font-family:Arial,Helvetica,sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f2f5;padding:30px 0;">
            <tr>
                <td align="center">
                    <table width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                        <tr>
                            <td style="background-color:#1877f2;padding:24px;text-align:center;">
                                <span style="color:#ffffff;font-size:22px;font-weight:bold;">Réseau Social</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:32px;">
                                <h2 style="color:#1c1e21;margin-top:0;">Bonjour ' . htmlspecialchars($prenom) . ',</h2>
                                <p style="color:#444;font-size:15px;line-height:1.5;">
                                    Merci de vous être inscrit(e) ! Pour activer votre compte, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous.
                                </p>
                                <div style="text-align:center;margin:28px 0;">
                                    <a href="' . htmlspecialchars($lienConfirmation) . '"
                                       style="background-color:#1877f2;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;font-weight:bold;display:inline-block;">
                                        Confirmer mon adresse email
                                    </a>
                                </div>
                                <p style="color:#777;font-size:13px;line-height:1.5;">
                                    Ce lien expire dans ' . DUREE_TOKEN_CONFIRMATION . ' heures. Si vous n’êtes pas à l’origine de cette inscription, ignorez simplement cet email.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color:#f7f7f7;padding:16px;text-align:center;">
                                <span style="color:#999;font-size:12px;">&copy; ' . date('Y') . ' Réseau Social — Tous droits réservés</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}

/**
 * Génère le template HTML de l'email de réinitialisation de mot de passe.
 */
function templateEmailResetPassword(string $prenom, string $lienReset): string
{
    return '
    <!DOCTYPE html>
    <html lang="fr">
    <head><meta charset="UTF-8"></head>
    <body style="margin:0;padding:0;background-color:#f0f2f5;font-family:Arial,Helvetica,sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f2f5;padding:30px 0;">
            <tr>
                <td align="center">
                    <table width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                        <tr>
                            <td style="background-color:#1877f2;padding:24px;text-align:center;">
                                <span style="color:#ffffff;font-size:22px;font-weight:bold;">Réseau Social</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:32px;">
                                <h2 style="color:#1c1e21;margin-top:0;">Bonjour ' . htmlspecialchars($prenom) . ',</h2>
                                <p style="color:#444;font-size:15px;line-height:1.5;">
                                    Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
                                </p>
                                <div style="text-align:center;margin:28px 0;">
                                    <a href="' . htmlspecialchars($lienReset) . '"
                                       style="background-color:#e7000b;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;font-weight:bold;display:inline-block;">
                                        Réinitialiser mon mot de passe
                                    </a>
                                </div>
                                <p style="color:#777;font-size:13px;line-height:1.5;">
                                    Ce lien expire dans ' . DUREE_TOKEN_RESET . ' heure(s). Si vous n’êtes pas à l’origine de cette demande, ignorez simplement cet email — votre mot de passe restera inchangé.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color:#f7f7f7;padding:16px;text-align:center;">
                                <span style="color:#999;font-size:12px;">&copy; ' . date('Y') . ' Réseau Social — Tous droits réservés</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}
