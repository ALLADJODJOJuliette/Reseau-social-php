const role = sessionStorage.getItem('admin_role')
if (!role) { window.location.href = 'admin-login.html';  }
if (role !== 'administrateur') {
    alert('Accès réservé aux administrateurs')
    window.location.href = 'dashboard.html'
}

let csrfToken = ''
fetch('../../api/admin/get-csrf-token.php')
    .then(response => response.json())
    .then(data => { csrfToken = data.token })

function chargerAdmins() {
    fetch('../../api/admin/get-users.php')
        .then(response => response.json())
        .then(data => {
            let htmlUsers = '', htmlModerateurs = '', htmlAdmins = ''
            data.forEach(user => {
                const ligne = `
                    <tr>
                        <td>${user.nom}</td><td>${user.prenom}</td><td>${user.email}</td>
                        <td>
                            ${user.role === 'user' ? `<button onclick="changerRole(${user.id}, 'moderateur')">Promouvoir modérateur</button>` : `<button onclick="changerRole(${user.id}, 'user')">Rétrograder en user</button>`}
                            <button onclick="supprimerCompte(${user.id})">Supprimer</button>
                        </td>
                    </tr>`
                if (user.role === 'user') htmlUsers += ligne
                else if (user.role === 'moderateur') htmlModerateurs += ligne
                else if (user.role === 'administrateur') htmlAdmins += ligne
            })
            document.getElementById('corps-users').innerHTML = htmlUsers
            document.getElementById('corps-moderateurs').innerHTML = htmlModerateurs
            document.getElementById('corps-administrateurs').innerHTML = htmlAdmins
        })
}
chargerAdmins()

function changerRole(id, nouveauRole) {
    if (!confirm('Voulez-vous vraiment changer le rôle de cet utilisateur ?')) { return }
    fetch('../../api/admin/changer-role.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, role: nouveauRole, csrfToken: csrfToken })
    })
    .then(response => response.json())
    .then(data => { if (data.success) { chargerAdmins() } else { alert(data.message) } })
}

function supprimerCompte(id) {
    if (!confirm('Voulez-vous vraiment supprimer définitivement ce compte ?')) { return }
    fetch('../../api/admin/delete-user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, csrfToken: csrfToken })
    })
    .then(response => response.json())
    .then(data => { if (data.success) { chargerAdmins() } else { alert(data.message) } })
}

function creerAdmin() {
    const nom = document.getElementById('nouveau-nom').value;
    const prenom = document.getElementById('nouveau-prenom').value;
    const email = document.getElementById('nouveau-email').value;
    const motDePasse = document.getElementById('nouveau-mdp').value;
    const role = document.getElementById('nouveau-role').value;

    if (!nom || !prenom || !email || !motDePasse) {
        alert('Veuillez remplir tous les champs');
        return;
    }

    fetch('../../api/admin/creer-admin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nom, prenom, email, motDePasse, role, csrfToken: csrfToken })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Compte créé avec succès');
            document.getElementById('formulaire-nouvel-admin').reset();
            chargerAdmins();
        } else {
            alert(data.message);
        }
    });
}