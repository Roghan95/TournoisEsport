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
    // console.log(tabName);
}

// Active le premier tab par défaut
const tabRegles = document.getElementById("tab-regles");
if (tabRegles != null) {
    tabRegles.click();
    console.log(tabRegles);
}
// ---------------------------------------------------------------------------

// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const chatIcon = document.getElementById('chat-icon');
    if(chatIcon != null){
        chatIcon.addEventListener('click', function () {
            const organisateurId = this.dataset.organisateurId;
            // console.log('organisateurId', organisateurId);
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
    }

});

// Fonction pour afficher le formulaire de modification de l'image de profil
function displayEditProfilePic() {
    console.log('displayEditProfilePic');
    const editProfilePic = document.getElementById('editProfilePic');
    const style = window.getComputedStyle(editProfilePic);
    if (style.getPropertyValue('display') ==='none') {
        editProfilePic.classList.remove('edit-profil-pic-hidden');
    } else {
        editProfilePic.classList.add('edit-profil-pic-hidden');
    }
}