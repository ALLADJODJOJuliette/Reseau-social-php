const role = sessionStorage.getItem('admin_role')
const id = sessionStorage.getItem('admin_id')
if (!role || !id) { window.location.href = 'admin-login.html'; return }

document.getElementById('info-role').innerText = 'Connecté en tant que : ' + role

fetch('../../api/admin/statistiques.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('nb-users').innerText = data.nbUsers;
        document.getElementById('nb-posts').innerText = data.nbPosts;
        document.getElementById('nb-messages').innerText = data.nbMessages;
        document.getElementById('nb-comments').innerText = data.nbComments;
        document.getElementById('nb-likes').innerText = data.nbLikes;
        document.getElementById('nb-admins').innerText = data.nbAdmins;
        document.getElementById('nb-moderateurs').innerText = data.nbModerateurs;
        document.getElementById('nb-users-simples').innerText = data.nbUsersSimples;
        document.getElementById('nb-suspendus').innerText = data.nbSuspendus;
        document.getElementById('nb-inscriptions-semaine').innerText = data.nbInscriptionsSemaine;
        document.getElementById('nb-demandes-attente').innerText = data.nbDemandesAttente;
    });