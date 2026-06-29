// assets/js/accueil.js


const userId = utilisateur.id;
const avatarParDefaut = '../../assets/images/default-avatar.png';

// ------------------------------------------------------------
// Affichage de la photo dans le formulaire de publication
// ------------------------------------------------------------
document.getElementById('form-photo').src = utilisateur.photo_profil
    ? '../../' + utilisateur.photo_profil
    : avatarParDefaut;

// ------------------------------------------------------------
// Chargement et affichage du fil d'actualité
// ------------------------------------------------------------
function chargerPosts() {
    fetch('../../api/posts/get_posts.php?user_id=' + userId)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error(data.message);
                return;
            }
            const filActualite = document.getElementById('fil-actualite');
            filActualite.innerHTML = '';
            data.posts.forEach(post => afficherPost(post));
        });
}

// ------------------------------------------------------------
// Construit et affiche un post à partir du template caché
// ------------------------------------------------------------
function afficherPost(post) {
    const template = document.getElementById('template-post');
    const clone = template.content.cloneNode(true);
    const carte = clone.querySelector('.post');

    carte.dataset.postId = post.id;

    const photo = post.photo_profil ? '../../' + post.photo_profil : avatarParDefaut;
    clone.querySelector('.post-photo').src = photo;
    clone.querySelector('.post-auteur').textContent = post.prenom + ' ' + post.nom;
    clone.querySelector('.post-date').textContent = formaterDate(post.date_publication);
    clone.querySelector('.post-contenu').textContent = post.contenu;

    if (post.image) {
        const imgEl = clone.querySelector('.post-image');
        imgEl.src = '../../' + post.image;
        imgEl.hidden = false;
    }

    clone.querySelector('.stat-likes').textContent = post.total_likes + ' réactions';
    clone.querySelector('.stat-comments').textContent = post.total_comments + ' commentaires';

    const boutonPrincipal = clone.querySelector('.post-actions .btn-reaction');
    if (post.ma_reaction) {
        boutonPrincipal.classList.add('active');
        boutonPrincipal.dataset.reaction = post.ma_reaction;
        boutonPrincipal.textContent = texteReaction(post.ma_reaction);
    }

    clone.querySelectorAll('.btn-reaction').forEach(bouton => {
        bouton.addEventListener('click', () => reagir(post.id, bouton.dataset.reaction));
    });

    clone.querySelector('.btn-commenter').addEventListener('click', () => {
        toggleCommentaires(carte, post.id);
    });

    // On ajoute le post au DOM AVANT de gérer l'affichage automatique des commentaires
    document.getElementById('fil-actualite').appendChild(clone);

    // S'il y a déjà des commentaires, on les affiche directement, sans clic nécessaire
    if (post.total_comments > 0) {
        ouvrirCommentaires(carte, post.id);
    }
}

function texteReaction(type) {
    if (type === 'coeur') return '❤️ Aimé';
    if (type === 'rire') return '😂 Drôle';
    return '👍 J\'aime';
}

function formaterDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
}

// ------------------------------------------------------------
// Publier un nouvel article
// ------------------------------------------------------------
document.getElementById('btn-publier').addEventListener('click', () => {
    const contenu = document.getElementById('post-contenu').value.trim();
    const fichierImage = document.getElementById('post-image').files[0];

    if (!contenu && !fichierImage) {
        alert('Écris quelque chose ou ajoute une image avant de publier.');
        return;
    }

    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('contenu', contenu);
    if (fichierImage) {
        formData.append('image', fichierImage);
    }

    fetch('../../api/posts/create_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('post-contenu').value = '';
            document.getElementById('post-image').value = '';
            document.getElementById('image-preview-nom').textContent = '';
            chargerPosts();
        } else {
            alert(data.message);
        }
    });
});

// Afficher le nom du fichier choisi
document.getElementById('post-image').addEventListener('change', e => {
    const fichier = e.target.files[0];
    document.getElementById('image-preview-nom').textContent = fichier ? fichier.name : '';
});

// ------------------------------------------------------------
// Réagir à un post (like/coeur/rire)
// ------------------------------------------------------------
function reagir(postId, typeReaction) {
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('post_id', postId);
    formData.append('type_reaction', typeReaction);

    fetch('../../api/likes/toggle_like.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            chargerPosts();
        } else {
            alert(data.message);
        }
    });
}

function toggleCommentaires(carte, postId) {
    const section = carte.querySelector('.comments-section');
    const estCache = section.hidden;

    if (estCache) {
        ouvrirCommentaires(carte, postId);
    } else {
        section.hidden = true;
    }
}

function chargerCommentaires(carte, postId) {
    fetch('../../api/comments/get_comments.php?user_id=' + userId + '&post_id=' + postId)
        .then(response => response.json())
        .then(data => {
            if (!data.success) return;
            const liste = carte.querySelector('.comments-list');
            liste.innerHTML = '';
            data.comments.forEach(c => afficherCommentaire(liste, c));
        });
}

function afficherCommentaire(liste, comment) {
    const template = document.getElementById('template-comment');
    const clone = template.content.cloneNode(true);

    const photo = comment.photo_profil ? '../../' + comment.photo_profil : avatarParDefaut;
    clone.querySelector('.comment-photo').src = photo;
    clone.querySelector('.comment-auteur').textContent = comment.prenom + ' ' + comment.nom;
    clone.querySelector('.comment-contenu').textContent = comment.contenu;
    clone.querySelector('.comment-date').textContent = formaterDate(comment.date_commentaire);

    liste.appendChild(clone);
}

function envoyerCommentaire(carte, postId, champ) {
    const contenu = champ.value.trim();
    if (!contenu) return;

    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('post_id', postId);
    formData.append('contenu', contenu);

    fetch('../../api/comments/add_comments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            champ.value = '';
            chargerCommentaires(carte, postId);
            chargerPosts(); // pour mettre à jour le compteur "X commentaires"
        } else {
            alert(data.message);
        }
    });
}
function ouvrirCommentaires(carte, postId) {
    const section = carte.querySelector('.comments-section');
    section.hidden = false;

    chargerCommentaires(carte, postId);

    const boutonEnvoyer = section.querySelector('.btn-envoyer-comment');
    const champ = section.querySelector('.comment-input');

    section.querySelector('.comment-photo').src = utilisateur.photo_profil
        ? '../../' + utilisateur.photo_profil
        : avatarParDefaut;

    boutonEnvoyer.onclick = () => envoyerCommentaire(carte, postId, champ);
}
// ------------------------------------------------------------
// Lancement initial
// ------------------------------------------------------------
chargerPosts();