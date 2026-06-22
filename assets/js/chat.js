const user_id = sessionStorage.getItem('user_id');
let contact_actif = null;
let intervalle = null;

document.addEventListener('DOMContentLoaded', () => {
    chargerConversations();
});

// ===== CONVERSATIONS =====
function chargerConversations() {
    fetch(`../../api/client/conversations_get.php?user_id=${user_id}`)
    .then(res => res.json())
    .then(conversations => {
        const div = document.getElementById('liste-conversations');
        div.innerHTML = '';

        if (conversations.length === 0) {
            div.innerHTML = '<p>Aucune conversation</p>';
            return;
        }

        conversations.forEach(c => {
            div.innerHTML += `
            <div class="conversation-card" onclick="ouvrirChat(${c.id}, '${c.nom} ${c.prenom}', '${c.photo_profil}')">
                <img src="${c.photo_profil ? '../../uploads/profils/'+c.photo_profil : '../../assets/images/default.png'}">
                <div>
                    <strong>${c.nom} ${c.prenom}</strong>
                    <p>${c.dernier_message ?? ''}</p>
                </div>
            </div>`;
        });
    });
}

// ===== OUVRIR UN CHAT =====
function ouvrirChat(id, nom, photo) {
    contact_actif = id;

    // Mettre à jour le header
    document.getElementById('chat-header').innerHTML = `
        <img src="${photo ? '../../uploads/profils/'+photo : '../../assets/images/default.png'}">
        <strong>${nom}</strong>
    `;

    // Charger les messages
    chargerMessages();

    // Rafraîchir toutes les 3 secondes
    if (intervalle) clearInterval(intervalle);
    intervalle = setInterval(chargerMessages, 3000);
}

// ===== CHARGER LES MESSAGES =====
function chargerMessages() {
    if (!contact_actif) return;

    fetch(`../../api/client/messages_get.php?user_id=${user_id}&contact_id=${contact_actif}`)
    .then(res => res.json())
    .then(messages => {
        const zone = document.getElementById('messages-zone');
        zone.innerHTML = '';

        messages.forEach(m => {
            const moi = m.expediteur_id == user_id;
            zone.innerHTML += `
            <div class="message ${moi ? 'moi' : 'autre'}">
                ${m.contenu ? `<p>${m.contenu}</p>` : ''}
                ${m.image ? `<img src="../../uploads/messages/${m.image}" class="msg-image">` : ''}
                <span class="heure">${new Date(m.date_envoi).toLocaleTimeString()}</span>
            </div>`;
        });

        // Scroll vers le bas
        zone.scrollTop = zone.scrollHeight;
    });
}

// ===== ENVOYER UN MESSAGE =====
function envoyerMessage() {
    if (!contact_actif) {
        alert('Sélectionne une conversation d\'abord');
        return;
    }

    const contenu = document.getElementById('input-message').value;
    const image = document.getElementById('input-image').files[0];

    if (!contenu && !image) return;

    const formData = new FormData();
    formData.append('expediteur_id', user_id);
    formData.append('destinataire_id', contact_actif);
    if (contenu) formData.append('contenu', contenu);
    if (image) formData.append('image', image);

    fetch('../../api/client/message_send.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('input-message').value = '';
            document.getElementById('input-image').value = '';
            chargerMessages();
            chargerConversations();
        }
    });
}

// ===== RECHERCHE D'AMIS =====
document.getElementById('recherche-ami')?.addEventListener('input', function() {
    const terme = this.value.toLowerCase();

    if (terme.length < 2) {
        document.getElementById('resultats-recherche').innerHTML = '';
        return;
    }

    fetch(`../../api/client/amis_get.php?user_id=${user_id}`)
    .then(res => res.json())
    .then(amis => {
        const div = document.getElementById('resultats-recherche');
        div.innerHTML = '';

        const filtres = amis.filter(a =>
            `${a.nom} ${a.prenom}`.toLowerCase().includes(terme)
        );

        filtres.forEach(a => {
            div.innerHTML += `
            <div class="conversation-card" onclick="ouvrirChat(${a.id}, '${a.nom} ${a.prenom}', '${a.photo_profil}')">
                <img src="${a.photo_profil ? '../../uploads/profils/'+a.photo_profil : '../../assets/images/default.png'}">
                <strong>${a.nom} ${a.prenom}</strong>
            </div>`;
        });
    });
});