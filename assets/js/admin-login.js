function connecterAdmin() {
    const email = document.getElementById('email').value;
    const motDePasse = document.getElementById('mot-de-passe').value;

    // Vérification des champs vides
    if (email === "" || motDePasse === "") {
         document.getElementById('message-erreur').innerText = "Veuillez remplir tous les champs.";

        return;
    }

}