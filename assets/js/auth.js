/**
 * auth.js — Logique d'authentification (inscription, connexion,
 * mot de passe oublié) via AJAX (fetch), sans rechargement de page.
 */

document.addEventListener('DOMContentLoaded', () => {
    initFormulaireInscription();
    initFormulaireConnexion();
    initFormulaireMotDePasseOublie();
    initFormulaireReinitialisation();
});

/* ============================================================
   INSCRIPTION
   ============================================================ */
function initFormulaireInscription() {
    const formulaire = document.getElementById('formulaireInscription');
    if (!formulaire) return;

    const messageAlerte = document.getElementById('messageAlerte');
    const bouton = document.getElementById('boutonInscription');

    formulaire.addEventListener('submit', async (evenement) => {
        evenement.preventDefault();
        cacherMessage(messageAlerte);

        const nom = document.getElementById('nom').value.trim();
        const prenom = document.getElementById('prenom').value.trim();
        const email = document.getElementById('email').value.trim();
        const motDePasse = document.getElementById('motDePasse').value;
        const confirmation = document.getElementById('confirmationMotDePasse').value;

        // ---- Validation côté client ----
        if (motDePasse !== confirmation) {
            afficherMessage(messageAlerte, 'Les mots de passe ne correspondent pas.', 'erreur');
            return;
        }

        const regexMotDePasse = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!regexMotDePasse.test(motDePasse)) {
            afficherMessage(messageAlerte, 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.', 'erreur');
            return;
        }

        basculerChargementBouton(bouton, true);

        try {
            const reponse = await appelAPI('inscription.php', 'POST', {
                nom, prenom, email, mot_de_passe: motDePasse
            });

            if (reponse.succes) {
                afficherMessage(messageAlerte, reponse.message + ' Vérifiez votre boîte email.', 'succes');
                formulaire.reset();
            } else {
                afficherMessage(messageAlerte, reponse.message, 'erreur');
            }
        } catch (erreur) {
            afficherMessage(messageAlerte, 'Erreur de connexion au serveur. Veuillez réessayer.', 'erreur');
            console.error(erreur);
        } finally {
            basculerChargementBouton(bouton, false, "S'inscrire");
        }
    });
}

/* ============================================================
   CONNEXION
   ============================================================ */
function initFormulaireConnexion() {
    const formulaire = document.getElementById('formulaireConnexion');
    if (!formulaire) return;

    const messageAlerte = document.getElementById('messageAlerte');
    const bouton = document.getElementById('boutonConnexion');

    formulaire.addEventListener('submit', async (evenement) => {
        evenement.preventDefault();
        cacherMessage(messageAlerte);

        const email = document.getElementById('email').value.trim();
        const motDePasse = document.getElementById('motDePasse').value;

        basculerChargementBouton(bouton, true);

        try {
            const reponse = await appelAPI('connexion.php', 'POST', {
                email, mot_de_passe: motDePasse
            });

            if (reponse.succes) {
                // ---- Stockage de la session côté client (sessionStorage) ----
                sessionStorage.setItem('utilisateur', JSON.stringify(reponse.utilisateur));
                window.location.href = 'accueil.html';
            } else {
                afficherMessage(messageAlerte, reponse.message, 'erreur');
            }
        } catch (erreur) {
            afficherMessage(messageAlerte, 'Erreur de connexion au serveur. Veuillez réessayer.', 'erreur');
            console.error(erreur);
        } finally {
            basculerChargementBouton(bouton, false, 'Se connecter');
        }
    });
}

/* ============================================================
   MOT DE PASSE OUBLIÉ — Étape 1 : demande du lien
   ============================================================ */
function initFormulaireMotDePasseOublie() {
    const formulaire = document.getElementById('formulaireMotDePasseOublie');
    if (!formulaire) return;

    const messageAlerte = document.getElementById('messageAlerte');
    const bouton = document.getElementById('boutonEnvoyer');

    formulaire.addEventListener('submit', async (evenement) => {
        evenement.preventDefault();
        cacherMessage(messageAlerte);

        const email = document.getElementById('email').value.trim();

        basculerChargementBouton(bouton, true);

        try {
            const reponse = await appelAPI('mot_de_passe_oublie.php', 'POST', { email });

            // Le serveur renvoie toujours succes=true avec un message générique,
            // pour ne pas révéler si l'email existe dans la base.
            afficherMessage(messageAlerte, reponse.message, reponse.succes ? 'succes' : 'erreur');
            if (reponse.succes) {
                formulaire.reset();
            }
        } catch (erreur) {
            afficherMessage(messageAlerte, 'Erreur de connexion au serveur. Veuillez réessayer.', 'erreur');
            console.error(erreur);
        } finally {
            basculerChargementBouton(bouton, false, 'Envoyer le lien de réinitialisation');
        }
    });
}

/* ============================================================
   MOT DE PASSE OUBLIÉ — Étape 2 : réinitialisation avec le token
   ============================================================ */
function initFormulaireReinitialisation() {
    const formulaire = document.getElementById('formulaireReinitialisation');
    if (!formulaire) return;

    const messageAlerte = document.getElementById('messageAlerte');
    const bouton = document.getElementById('boutonReinitialiser');

    // ---- Récupération du token depuis l'URL (?token=...) ----
    const parametresURL = new URLSearchParams(window.location.search);
    const token = parametresURL.get('token');

    if (!token) {
        afficherMessage(messageAlerte, 'Lien invalide : aucun token trouvé dans l’URL.', 'erreur');
        formulaire.querySelector('button').disabled = true;
        return;
    }

    formulaire.addEventListener('submit', async (evenement) => {
        evenement.preventDefault();
        cacherMessage(messageAlerte);

        const nouveauMotDePasse = document.getElementById('nouveauMotDePasse').value;
        const confirmation = document.getElementById('confirmationMotDePasse').value;

        if (nouveauMotDePasse !== confirmation) {
            afficherMessage(messageAlerte, 'Les mots de passe ne correspondent pas.', 'erreur');
            return;
        }

        const regexMotDePasse = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!regexMotDePasse.test(nouveauMotDePasse)) {
            afficherMessage(messageAlerte, 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.', 'erreur');
            return;
        }

        basculerChargementBouton(bouton, true);

        try {
            const reponse = await appelAPI('reinitialiser_mot_de_passe.php', 'POST', {
                token, nouveau_mot_de_passe: nouveauMotDePasse
            });

            afficherMessage(messageAlerte, reponse.message, reponse.succes ? 'succes' : 'erreur');

            if (reponse.succes) {
                formulaire.reset();
                setTimeout(() => { window.location.href = 'connexion.html'; }, 2000);
            }
        } catch (erreur) {
            afficherMessage(messageAlerte, 'Erreur de connexion au serveur. Veuillez réessayer.', 'erreur');
            console.error(erreur);
        } finally {
            basculerChargementBouton(bouton, false, 'Réinitialiser le mot de passe');
        }
    });
}
