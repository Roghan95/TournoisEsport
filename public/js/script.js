// Vérifier si un jeu est déjà sélectionné dans le localStorage
document.addEventListener('DOMContentLoaded', function() { 
    var storedGameName = localStorage.getItem('selectedGameName');
    if (storedGameName) {
        document.getElementById('selectGameButton').textContent = storedGameName;
    }

    console.log('test');
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
window.onclick = function(event) {
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
