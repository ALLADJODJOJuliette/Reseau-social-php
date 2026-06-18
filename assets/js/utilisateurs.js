const role = sessionStorage.getItem('admin_role')
if (!role) {
    window.location.href = 'admin-login.html'
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
                html += `
                    <tr>
                        <td><img src="../../assets/images/${user.photo_profil || 'default.png'}" width="40"></td>
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