function connecterAdmin() {
    const email = document.getElementById('email').value;
    const motDePasse = document.getElementById('mot-de-passe').value;

    // Vérification des champs vides
    if (email === "" || motDePasse === "") {
         document.getElementById('message-erreur').innerText = "Veuillez remplir tous les champs.";

        return;
    }
    fetch('../../api/admin/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body:JSON.stringify({ email: email,
             motDePasse: motDePasse })
    })
    .then(response => response.json())
    .then(data => {
            if (data.success) {
                sessionStorage.setItem('admin_role', data.role)
                sessionStorage.setItem('admin_id', data.id)
                window.location.href = 'dashboard.html';
            } else {
                document.getElementById('message-erreur').innerText = data.message;
            }
    })
 }    