const role = sessionStorage.getItem('admin_role')
if (!role) {
    window.location.href = 'admin-login.html'; 
}

let csrfToken = ''

fetch('../../api/admin/get-csrf-token.php')
    .then(response => response.json())
    .then(data => {
        csrfToken = data.token
    })
function chargerUsers() {
    fetch('../../api/admin/get-users.php')
        .then(response => response.json())
        .then(data => {
            let html = ''
            data.forEach(user => {
                    let avatarParDefaut = 'assets/images/default-avatar.png';
if (user.sexe === 'homme') avatarParDefaut = 'assets/images/default-avatar-homme.png';
if (user.sexe === 'femme') avatarParDefaut = 'assets/images/default-avatar-femme.png';

const photoEstParDefaut = !user.photo_profil || user.photo_profil.includes('default-avatar');
const photoAAfficher = photoEstParDefaut ? avatarParDefaut : user.photo_profil;
                html += `
                    <tr>
                        <td><img src="../../${photoAAfficher}" width="40"></td>
                        <td>${user.nom}</td>
                        <td>${user.prenom}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td>${user.statut}</td>
                        <td><button onclick="supprimerUser(${user.id})">Supprimer</button></td>
                    </tr>
                `
            })
            document.getElementById('corps-tableau').innerHTML = html
        })
}
chargerUsers()    

function supprimerUser(id) {
    if (!confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
        return
    }

    fetch('../../api/admin/delete-user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, csrfToken: csrfToken })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            chargerUsers()
        } else {
            alert(data.message)
        }
    })
}
function rechercherProfil() {
    const recherche = document.getElementById('champ-recherche').value;
    if (!recherche) {
        alert('Tape un nom, prénom ou email');
        return;
    }

    fetch('../../api/admin/profil-utlisateur.php?recherche=' + encodeURIComponent(recherche))
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('resultat-profil').innerHTML = '<p>' + data.message + '</p>';
                return;
            }

            let htmlTypes = '';
            data.typesLikes.forEach(t => {
                htmlTypes += `${t.type_reaction} : ${t.total}<br>`;
            });

            document.getElementById('resultat-profil').innerHTML = `
                <div class="carte-profil">
                    <h3>${data.prenom} ${data.nom}</h3>
                    <p>Email : ${data.email}</p>
                    <p>Rôle : ${data.role} — Statut : ${data.statut}</p>
                    <p>Inscrit le : ${data.date_creation}</p>
                    <hr>
                    <p>Articles publiés : ${data.nbPosts}</p>
                    <p>Commentaires laissés : ${data.nbComments}</p>
                    <p>Réactions données : ${data.nbLikesDonnes}</p>
                    <p>Détail des réactions : <br>${htmlTypes}</p>
                    <p>Messages envoyés : ${data.nbMessagesEnvoyes}</p>
                    <p>Messages reçus : ${data.nbMessagesRecus}</p>
                    <p>Amis : ${data.nbAmis}</p>
                </div>
            `;
        });
}