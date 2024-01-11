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
// ---------------------------------------------------------------------------

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
                if (result == true) {
                    let pseudo = data.pseudo;
                    changeJoinBtnState();
                    addUserToListParticipants(pseudo);
                }

            })
            .catch(error => console.error('Error:', error));
    }

    // Fonction asynchrone pour sauvegarder le nouveau pseudo
    async function saveNewPseudo(pseudo) {
        let response = await fetch('save-new-pseudo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            // On envoie le pseudo et l'id du jeu et on encode en JSON
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

    // récupère le bouton submit de la modale
    let btnSubmitPseudo = document.getElementById("btnSubmitPseudo");

    // écoute le click sur le bouton
    btnSubmitPseudo.addEventListener('click', async function (e) {
        e.preventDefault();
        let pseudo = document.getElementById("pseudo").value;
        await saveNewPseudo(pseudo);
        addUserTournoi();

        document.getElementById('pseudoModal').style.display = 'none';
    });


    // On change le texte du bouton et on le désactive
    function changeJoinBtnState() {
        btnParticiper.innerHTML = "Vous participez à ce tournoi";
        btnParticiper.disabled = true;
    }

    function addUserToListParticipants(pseudo) {
        const listParticipants = document.getElementById('participants');
        const p = document.createElement('p');
        p.classList.add('participant');
        p.innerHTML = pseudo;
        listParticipants.appendChild(p);

        const nbJoueurs = document.getElementById('nbJoueurs');
        let nbJoueursInt = parseInt(nbJoueurs.innerHTML);
        nbJoueursInt++;
        nbJoueurs.innerHTML = nbJoueursInt;
    }

    document.getElementById('chat-icon').addEventListener('click', function() {
        fetch('/create-room', { 
            method: 'POST',
            body: JSON.stringify({ tournoiId: '{{ tournoi.id }}' }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.roomId) {
                window.location.href = `/chat?room=${data.roomId}`;
            }
        });
    });
});


