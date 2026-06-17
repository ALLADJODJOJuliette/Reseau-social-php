// Récupérer l'id de l'utilisateur connecté depuis sessionStorage
const user_id = sessionStorage.getItem('user_id');

// ===== CHARGER LE PROFIL AU DÉMARRAGE =====
document.addEventListener('DOMContentLoaded', () => {
    chargerProfil();
});

function chargerProfil() {
    fetch(`../../api/profil_get.php?user_id=${user_id}`)
    .then(res => res.json())
    .then(data => {
        if (data.erreur) return;

        document.getElementById('nom').value = data.nom;
        document.getElementById('prenom').value = data.prenom;
        document.getElementById('email').value = data.email;
        document.getElementById('telephone').value = data.telephone;
        document.getElementById('date_naissance').value = data.date_naissance;
        document.getElementById('sexe').value = data.sexe;

        if (data.photo_profil) {
            document.getElementById('photo-profil').src = 
            `../../uploads/profils/${data.photo_profil}`;
        }
    });
}

// ===== MODIFIER LES INFOS =====
function modifierProfil() {
    const data = {
        user_id: user_id,
        nom: document.getElementById('nom').value,
        prenom: document.getElementById('prenom').value,
        email: document.getElementById('email').value,
        telephone: document.getElementById('telephone').value,
        date_naissance: document.getElementById('date_naissance').value,
        sexe: document.getElementById('sexe').value
    };

    fetch('../../api/profil_update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('msg-profil').textContent = data.message;
        document.getElementById('msg-profil').style.color = 'green';
    });
}

// ===== CHANGER LE MOT DE PASSE =====
function changerMotDePasse() {
    const ancien = document.getElementById('ancien-mdp').value;
    const nouveau = document.getElementById('nouveau-mdp').value;
    const confirm = document.getElementById('confirm-mdp').value;

    if (nouveau !== confirm) {
        document.getElementById('msg-mdp').textContent = 'Les mots de passe ne correspondent pas';
        document.getElementById('msg-mdp').style.color = 'red';
        return;
    }

    fetch('../../api/password_update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: user_id,
            ancien_mot_de_passe: ancien,
            nouveau_mot_de_passe: nouveau
        })
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('msg-mdp');
        msg.textContent = data.message;
        msg.style.color = data.success ? 'green' : 'red';
    });
}

// ===== CHANGER LA PHOTO =====
function changerPhoto() {
    const input = document.getElementById('input-photo');
    const file = input.files[0];

    if (!file) {
        alert('Sélectionne une photo d\'abord');
        return;
    }

    const formData = new FormData();
    formData.append('photo', file);
    formData.append('user_id', user_id);

    fetch('../../api/photo_update.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('photo-profil').src = 
            `../../uploads/profils/${data.photo}`;
            alert('Photo mise à jour !');
        }
    });
}