function chargerNav() {
    const role = sessionStorage.getItem('admin_role')

    let htmlNav = `
        <nav>
            <a href="dashboard.html">Dashboard</a>
            <a href="utilisateurs.html">Utilisateurs</a>
            <a href="articles.html">Articles</a>
    `

    if (role === 'administrateur') {
        htmlNav += `<a href="admins.html">Admins</a>`
    }

    htmlNav += `
            <button onclick="deconnexion()">Déconnexion</button>
        </nav>
    `

    document.getElementById('nav').innerHTML = htmlNav
}

function deconnexion() {
    sessionStorage.clear()
    window.location.href = 'admin-login.html'
}

chargerNav()