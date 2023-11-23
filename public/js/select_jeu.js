document.addEventListener('DOMContentLoaded', function () {
    // récupère le jeu sélectionné 
    let jeuSelect = document.getElementById("jeu-select");

    jeuSelect.addEventListener('change', function () {
        var jeuId = this.value;
        console.log('jeuId', jeuId);
        selectJeu(jeuId);
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