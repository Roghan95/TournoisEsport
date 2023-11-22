document.addEventListener('DOMContentLoaded', function () {
    // séléctionner le jeu
    let jeuSelect = document.getElementById("jeu-select");

    jeuSelect.addEventListener('change', function () {
        var jeuId = this.value;
        console.log('jeuId', jeuId);
        selectJeu(jeuId);
    });

});


function selectJeu(jeuId) {
    fetch('/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "jeuId": jeuId
        })
    }).then(response => {
        // Rechargez la page après que la requête a été envoyée
        console.log('response', response);
        location.reload();
    });
}