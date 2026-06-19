const role = sessionStorage.getItem('admin_role');
if (!role) { window.location.href = 'admin-login.html'; return; }

let csrfToken = '';
fetch('../../api/admin/get-csrf-token.php')
    .then(response => response.json())
    .then(data => { csrfToken = data.token; });

function chargerPosts() {
    fetch('../../api/admin/get-posts.php')
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(post => {
                html += `
                    <tr>
                        <td>${post.nom} ${post.prenom}</td>
                        <td>${post.contenu}</td>
                        <td>${post.image ? `<img src="../../assets/images/${post.image}" style="max-width:60px;">` : 'Aucune image'}</td>
                        <td>${post.date_publication}</td>
                        <td><button onclick="supprimerPost(${post.id})">Supprimer</button></td>
                    </tr>
                `;
            });
            document.getElementById('corps-tableau').innerHTML = html;
        });
}
chargerPosts();

function supprimerPost(id) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cet article ?")) { return; }
    fetch('../../api/admin/delete-post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, csrfToken: csrfToken })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) { chargerPosts(); } else { alert(data.message); }
    });
}