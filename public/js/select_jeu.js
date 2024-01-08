document.addEventListener('DOMContentLoaded', function () {
    let jeuxListe = document.querySelector(".jeux-liste");
    let summary = document.querySelector("#jeux-dropdown summary");

    jeuxListe.addEventListener('click', function (e) {
        // Trouver l'élément 'li' le plus proche, peu importe où le clic a eu lieu
        let targetLi = e.target.closest('li');
        if (targetLi) {
            var jeuId = targetLi.getAttribute('data-value');
            var jeuNom = targetLi.querySelector('.nomJeu').textContent; // Récupérer le nom du jeu
            console.log('jeuId', jeuId);
            selectJeu(jeuId);

            // Mettre à jour le résumé pour afficher le jeu sélectionné
            summary.textContent = jeuNom; // Mettre à jour le texte de summary
        }
    });
});

// Sélectionner le jeu
function selectJeu(jeuId) {
    fetch('/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "jeuId": jeuId
        })
        // une fois un jeu sélectionné, rechargez la page
    }).then(response => {
        // Rechargez la page après que la requête a été envoyée
        console.log('response', response);
        location.reload();
    });
}