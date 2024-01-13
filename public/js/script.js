// Toggle pour afficher/cacher la liste déroulante
// function showDropdown() {
//     document.getElementById("myDropdown").classList.toggle("show");
// }

// // Gérer la fermeture de la liste déroulante lorsque l'utilisateur clique en dehors
// window.onclick = function (event) {
//     if (!event.target.matches('.dropbtn')) {
//         var dropdowns = document.getElementsByClassName("dropdown-content");
//         var i;
//         for (i = 0; i < dropdowns.length; i++) {
//             var openDropdown = dropdowns[i];
//             if (openDropdown.classList.contains('show')) {
//                 openDropdown.classList.remove('show');
//             }
//         }
//     }
// }

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
const tabRegles = document.getElementById("tab-regles");
if (tabRegles != null) {
    tabRegles.click();
}
// ---------------------------------------------------------------------------

// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('chat-icon').addEventListener('click', function () {
        const organisateurId = this.dataset.organisateurId;
        console.log('organisateurId', organisateurId);
        fetch('/chat/create-room', {
            method: 'POST',
            body: JSON.stringify({ userId: organisateurId }),
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


