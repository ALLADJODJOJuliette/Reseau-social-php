// Vérification de la connexion de l'administrateur sinon redirection
const role = sessionStorage.getItem('admin_role')
const id = sessionStorage.getItem('admin_id')
if (!role || !id) {
    window.location.href = 'admin-login.html'
}

// Afficher le rôle à l'écran du tableau de bord
document.getElementById('info-role').innerText = 'Connecté en tant que : ' + role

// Charger les statistiques du tableau de bord depuis le fichier statistiques.php
fetch('../../api/admin/statistiques.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('nb-users').innerText = data.nbUsers;
        document.getElementById('nb-posts').innerText = data.nbPosts;
        document.getElementById('nb-messages').innerText = data.nbMessages;
    });