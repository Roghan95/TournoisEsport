// Vérifier si un jeu est déjà sélectionné dans le localStorage
document.addEventListener('DOMContentLoaded', function () {
    var storedGameName = localStorage.getItem('selectedGameName');
    if (storedGameName) {
        document.getElementById('selectGameButton').textContent = storedGameName;
    }
});

// Fonction pour sélectionner un jeu
function selectGame(id, nomJeu) {
    localStorage.setItem('selectedGameId', id); // Stocker l'id du jeu dans le localStorage
    localStorage.setItem('selectedGameName', nomJeu); // Stocker le jeu dans le localStorage
    document.getElementById('selectGameButton').textContent = nomJeu; // Mettre à jour le bouton
    document.getElementById("myDropdown").classList.remove("show"); // Fermer la liste déroulante
}

// Toggle pour afficher/cacher la liste déroulante
function showDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Gérer la fermeture de la liste déroulante lorsque l'utilisateur clique en dehors
window.onclick = function (event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

//? TAB BAR REGLES PARTICIPANTS MATCHS D'UN TOURNOI
function openTab(tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tab");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" tab-active", "");
    }

    document.getElementById(tabName).style.display = "block";
    event.currentTarget.className += " tab-active";
}

// Active le premier tab par défaut
document.getElementById("tab-regles").click();
// --------------------------------------------------

// Function qui intércèpte le formulaire qui permet de rejoindre un tournoi
// document.addEventListener('DOMContentLoaded', function () {
//     let btnParticiper = document.getElementById("btnParticiper");
//     let tournoiId = btnParticiper.dataset.tournoiId;
//     console.log('tournoiId', tournoiId);
//     btnParticiper.addEventListener('click', function (e) {
//         e.preventDefault();

//         fetch('join-tournoi', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json'
//             },
//             body: JSON.stringify({
//                 "tournoiId": tournoiId
//             })
//         })
//             .then(response => response.json())
//             .then(data => {
//                 console.log('reponse:', data);
//                 let result = data.success;
//                 console.log('result', result);
//                 // Traiter la réponse
//             })
//             .catch(error => console.error('Error:', error));
//     });
// });
// ---------------------------------------------------------------------------


document.addEventListener('DOMContentLoaded', function () {

    let btnParticiper = document.getElementById("btnParticiper");
    let jeuId = btnParticiper.dataset.jeuId;
    let tournoiId = btnParticiper.dataset.tournoiId;
    // Ouvrir la modale
    btnParticiper.addEventListener('click', function (e) {
        e.preventDefault();
        fetch(`check-pseudo/${jeuId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        })
            .then(response => response.json())
            .then(data => {
                console.log('reponse:', data);
                let result = data.success;
                console.log('result', result);
                if (result == false) {
                    document.getElementById('pseudoModal').style.display = 'block';
                }
                else {
                    addUserTournoi();
                }
            })
            .catch(error => console.error('Error:', error));

    });

    // Fermer la modale
    document.querySelector('.close-btn').addEventListener('click', function () {
        document.getElementById('pseudoModal').style.display = 'none';
    });


    // Ajouter l'utilisateur dans le tournoi
    function addUserTournoi() {
        fetch('join-tournoi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "tournoiId": tournoiId
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log('reponse:', data);
                let result = data.success;
                console.log('result', result);
                // Traiter la réponse
            })
            .catch(error => console.error('Error:', error));
    }

    async function saveNewPseudo(pseudo) {
        let response = await fetch('save-new-pseudo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "pseudo": pseudo,
                "jeuId": jeuId
            })
        });
        let data = await response.json();
        console.log('data', data);
        let result = data.success;
        console.log('result', result);
        return result;

    }

    let btnSubmitPseudo = document.getElementById("btnSubmitPseudo");

    btnSubmitPseudo.addEventListener('click', async function (e) {
        e.preventDefault();
        let pseudo = document.getElementById("pseudo").value;
        await saveNewPseudo(pseudo);
        addUserTournoi();

        document.getElementById('pseudoModal').style.display = 'none';
    });

});
