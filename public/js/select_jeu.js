// document.addEventListener('DOMContentLoaded', function () {
//     // récupère le jeu sélectionné 
//     let jeuSelect = document.getElementById("jeu-select");

//     jeuSelect.addEventListener('change', function () {
//         var jeuId = this.value;
//         console.log('jeuId', jeuId);
//         selectJeu(jeuId);
//     });
// });


document.addEventListener('DOMContentLoaded', function () {
    let jeuxListe = document.querySelector(".jeux-liste");
    let summary = document.querySelector("#jeu-details summary");

    jeuxListe.addEventListener('click', function (e) {
        // Trouver l'élément 'li' le plus proche, peu importe où le clic a eu lieu
        let targetLi = e.target.closest('li');
        if (targetLi) {
            var jeuId = targetLi.getAttribute('data-value');
            console.log('jeuId', jeuId);
            selectJeu(jeuId);

            // Mettre à jour le résumé pour afficher le jeu sélectionné
            summary.textContent = targetLi.textContent.trim();
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