const user_id = sessionStorage.getItem('user_id');

document.addEventListener('DOMContentLoaded', () => {
    chargerUtilisateurs();
    chargerAmis();
    chargerDemandesRecues();
});

// ===== TOUS LES UTILISATEURS =====
function chargerUtilisateurs() {
    fetch(../../api/admin/amis_liste.php?user_id=${user_id})
    .then(res => res.json())
    .then(users => {
        const div = document.getElementById('liste-utilisateurs');
        div.innerHTML = '';
        users.forEach(u => {
            div.innerHTML += `
            <div class="user-card">
                <img src="${u.photo_profil ? '../../uploads/profils/'+u.photo_profil : '../../assets/images/default.png'}">
                <span>${u.nom} ${u.prenom}</span>
                ${boutonAmi(u)}
            </div>`;
        });
    });
}

function boutonAmi(u) {
    if (u.statut_ami === 'accepte') return '<span class="badge vert">Ami ✓</span>';
    if (u.statut_ami === 'en_attente') return '<span class="badge orange">En attente...</span>';
    return <button onclick="envoyerDemande(${u.id})">Ajouter</button>;
}

// ===== ENVOYER UNE DEMANDE =====
function envoyerDemande(friend_id) {
    fetch('../../api/admin/amis_envoyer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id, friend_id })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        chargerUtilisateurs();
    });
}

// ===== DEMANDES REÇUES =====
function chargerDemandesRecues() {
    fetch(../../api/admin/amis_liste.php?user_id=${user_id})
    .then(res => res.json())
    .then(users => {
        const div = document.getElementById('demandes-recues');
        div.innerHTML = '';
        const demandes = users.filter(u => u.statut_ami === 'en_attente');
        if (demandes.length === 0) {
            div.innerHTML = '<p>Aucune demande reçue</p>';
            return;
        }
        demandes.forEach(u => {
            div.innerHTML += `
            <div class="user-card">
                <img src="${u.photo_profil ? '../../uploads/profils/'+u.photo_profil : '../../assets/images/default.png'}">
                <span>${u.nom} ${u.prenom}</span>
                <button class="vert" onclick="repondre(${u.friendship_id}, 'accepte')">Accepter</button>
                <button class="rouge" onclick="repondre(${u.friendship_id}, 'refuse')">Refuser</button>
            </div>`;
        });
    });
}

// ===== RÉPONDRE À UNE DEMANDE =====
function repondre(friendship_id, statut) {
    fetch('../../api/admin/amis_repondre.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ friendship_id, statut })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        chargerDemandesRecues();
        chargerAmis();
    });
}

// ===== MES AMIS =====
function chargerAmis() {
    fetch(../../api/admin/amis_get.php?user_id=${user_id})
    .then(res => res.json())
    .then(amis => {
        const div = document.getElementById('liste-amis');
        div.innerHTML = '';
        if (amis.length === 0) {
            div.innerHTML = '<p>Aucun ami pour le moment</p>';
            return;
        }
        amis.forEach(a => {
            div.innerHTML += `
            <div class="user-card">
                <img src="${a.photo_profil ? '../../uploads/profils/'+a.photo_profil : '../../assets/images/default.png'}">
                <span>${a.nom} ${a.prenom}</span>
            </div>`;
        });
    });
}

// ===== RECHERCHE =====
document.getElementById('recherche')?.addEventListener('input', function() {
    const terme = this.value.toLowerCase();
    const cards = document.querySelectorAll('#liste-utilisateurs .user-card');
    cards.forEach(card => {
        const nom = card.querySelector('span').textContent.toLowerCase();
        card.style.display = nom.includes(terme) ? 'flex' : 'none';
    });
});