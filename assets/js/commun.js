/**
 * Helpers communs - Réseau Social
 * Fonctions réutilisables pour les appels AJAX et l'affichage des messages.
 */

// URL de base de l'API (à adapter selon l'environnement de déploiement)
const URL_API = '/reseau-social/api';

/**
 * Effectue une requête AJAX (fetch) vers l'API PHP.
 * @param {string} endpoint - chemin relatif de l'API (ex: 'inscription.php')
 * @param {string} methode - GET, POST, etc.
 * @param {object|null} corps - données à envoyer en JSON
 * @returns {Promise<object>} la réponse JSON parsée
 */
async function appelAPI(endpoint, methode = 'POST', corps = null) {
    const options = {
        method: methode,
        headers: { 'Content-Type': 'application/json' },
    };
    if (corps !== null) {
        options.body = JSON.stringify(corps);
    }

    const reponse = await fetch(`${URL_API}/${endpoint}`, options);
    const donnees = await reponse.json();
    return donnees;
}

/**
 * Affiche un message d'alerte (erreur ou succès) dans un conteneur donné.
 * @param {HTMLElement} conteneur - élément où afficher le message
 * @param {string} message - texte à afficher
 * @param {string} type - 'erreur' ou 'succes'
 */
function afficherMessage(conteneur, message, type = 'erreur') {
    conteneur.textContent = message;
    conteneur.className = `message-alerte ${type} visible`;
}

/**
 * Cache un message d'alerte.
 */
function cacherMessage(conteneur) {
    conteneur.className = 'message-alerte';
}

/**
 * Active/désactive l'état "chargement" d'un bouton de soumission.
 */
function basculerChargementBouton(bouton, enChargement, texteNormal) {
    if (enChargement) {
        bouton.disabled = true;
        bouton.innerHTML = '<span class="spinner"></span> Veuillez patienter...';
    } else {
        bouton.disabled = false;
        bouton.textContent = texteNormal;
    }
}

/**
 * Redirige vers la page de connexion si aucun utilisateur n'est en session.
 * À appeler en haut des pages protégées (accueil, profil, amis, chat...).
 */
function exigerConnexion() {
    if (!sessionStorage.getItem('utilisateur')) {
        window.location.href = 'connexion.html';
    }
}

/**
 * Récupère l'utilisateur actuellement connecté depuis sessionStorage.
 * @returns {object|null}
 */
function utilisateurConnecte() {
    const donnees = sessionStorage.getItem('utilisateur');
    return donnees ? JSON.parse(donnees) : null;
}

/**
 * Déconnecte l'utilisateur (vide sessionStorage et redirige).
 */
function deconnecter() {
    sessionStorage.removeItem('utilisateur');
    window.location.href = 'connexion.html';
}
