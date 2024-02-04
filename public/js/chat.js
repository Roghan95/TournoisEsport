document.addEventListener('DOMContentLoaded', function () {
    const chat = document.getElementById('chat');
    const userId = chat.dataset.userId;
    const messageTextElem = document.getElementById('message-text');
    const messageForm = document.getElementsByClassName('message-form');
    const sendMessageForm = document.getElementById('send-message-form');
    const messagesDiv = document.getElementById('messages');
    const roomItems = document.querySelectorAll('.room-item');
    let roomElements = document.querySelector('.rooms');
    let messageContainer = document.querySelector('.message-container');
    let faSolid = document.querySelector('.fa-solid');
    let faArrow = document.querySelector('.fa-arrow-left');

    messageTextElem.addEventListener('keypress', handleKeyPress);
    sendMessageForm.addEventListener('submit', handleSubmit);
    roomItems.forEach(room => room.addEventListener('click', handleRoomClick));

    // function pour pouvoir envoyer un message en appuyant sur la touche "Entrée" et sauter une ligne en appuyant sur "Shift + Entrée"
    async function handleKeyPress(e) {
        if ((e.key === 'Enter' || e.keyCode === 13) && !e.shiftKey) {
            e.preventDefault();
            sendMessageForm.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    }

    //* Envoyer un message dans la room active
    async function handleSubmit(e) {
        e.preventDefault();
        const roomId = sendMessageForm.dataset.roomId;
        // On supprime les espaces vide au début et à la fin du message (trim())
        const messageText = messageTextElem.value.trim();
        if (!messageText) return;

        messageTextElem.value = '';
        try {
            const newMessage = await sendMessage(roomId, messageText);
            if (newMessage) {
                appendMessage(newMessage, userId);
            }
        } catch (error) {
            console.error("Error sending message:", error);
            //* Afficher un message d'erreur à l'utilisateur
        }
    }

    //* Changer la room active et afficher les messages de cette room
    async function handleRoomClick(e) {
        e.preventDefault();
        const roomId = this.dataset.roomId;
        setActiveRoom(roomId)
    }

    function addClassesToActiveRoom(roomId) {
        roomItems.forEach(room => {
            room.classList.toggle('room-item-active', room.dataset.roomId === roomId);
        });
        sendMessageForm.setAttribute('data-room-id', roomId);
        sendMessageForm.classList.add('show-form');
    }

    const roomId = document.getElementById('chat').dataset.activeRoomId;

    if (roomId) {
        setActiveRoom(roomId);
    }

    async function setActiveRoom(roomId) {
        addClassesToActiveRoom(roomId);
        const messages = await getMessages(roomId);
        displayMessages(messages, userId);
    }

    function displayMessages(messages, userId) {
        messagesDiv.innerHTML = '';
        messages.forEach(message => appendMessage(message, userId));
        scrollToBottom();
    }

    // Déclaration de la fonction "appendMessage" qui prend en paramètres "message" et "userId"
    function appendMessage(message, userId) {
        // Création de nouveaux éléments HTML
        let messageWrapperDiv = document.createElement('div');
        let messageContentDiv = document.createElement('div');
        let pseudoSpan = document.createElement('span');
        let textSpan = document.createElement('span');
        let dateSpan = document.createElement('span');

        // Attribution du contenu textuel aux éléments HTML créés
        pseudoSpan.textContent = message.expediteur.pseudo + " : ";
        textSpan.textContent = message.texteMessage;
        dateSpan.textContent = new Date(message.createdAt).toLocaleDateString("fr", { hour: '2-digit', minute: '2-digit' });

        // Si l'ID de l'expéditeur du message est le même que l'ID de l'utilisateur
        if (message.expediteur.id == userId) {
            // Ajout de classes CSS aux éléments HTML pour styliser les messages envoyés par l'utilisateur
            messageWrapperDiv.classList.add('message-right');
            messageContentDiv.classList.add('message-sent');
            pseudoSpan.classList.add('sender-pseudo');
            dateSpan.classList.add('sender-date');
        } else {
            // Ajout de classes CSS aux éléments HTML pour styliser les messages reçus d'autres utilisateurs
            messageWrapperDiv.classList.add('message-left');
            messageContentDiv.classList.add('message-received');
            pseudoSpan.classList.add('receiver-pseudo');
            dateSpan.classList.add('receiver-date');
        }

        // Ajout des éléments span au div de contenu du message
        messageContentDiv.appendChild(pseudoSpan);
        messageContentDiv.appendChild(textSpan);
        messageContentDiv.appendChild(dateSpan);
        // Ajout du div de contenu du message au div enveloppe du message
        messageWrapperDiv.appendChild(messageContentDiv);

        // Sélection du div contenant tous les messages
        let messagesDiv = document.getElementById('messages');
        // Ajout du div enveloppe du message au div contenant tous les messages
        messagesDiv.appendChild(messageWrapperDiv);
        // Défilement vers le bas du div contenant tous les messages
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // Déclaration de la fonction "scrollToBottom"
    function scrollToBottom() {
        // Défilement vers le bas du div contenant tous les messages
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // Déclaration d'une fonction asynchrone appelée "getMessages" qui prend en paramètre "roomId" qui permet de récupérer les messages d'une room
    async function getMessages(roomId) {
        // Bloc try pour capturer les erreurs
        try {
            // Envoi d'une requête fetch à l'URL 'chat/room/{roomId}' avec la méthode GET
            const response = await fetch(`chat/room/${roomId}`, {
                method: 'GET', // Spécifie la méthode de la requête HTTP
                headers: { 'Content-Type': 'application/json' } // Définit le type de contenu de la requête
            });
            // Si la réponse n'est pas OK (statut HTTP 200-299), lance une erreur
            if (!response.ok) throw new Error('Failed to fetch messages');
            // Passer les rooms en display none
            roomElements.style.display = 'none';
            messageContainer.style.display = 'flex';
            // faSolid.style.display = 'none';
            faArrow.style.display = 'block';

            // Renvoie la réponse convertie en JSON
            return await response.json();
            // Bloc catch pour gérer les erreurs
        } catch (error) {
            console.error("Error fetching messages:", error);
            //* Afficher un message d'erreur à l'utilisateur
            alert('Une erreur est survenue lors de la récupération des messages : ' + error.message);
        }
    }

    // Déclaration d'une fonction asynchrone appelée "sendMessage" qui prend en paramètres "roomId" et "message"
    async function sendMessage(roomId, message) {
        // Bloc try pour capturer les erreurs
        try {
            // Envoi d'une requête fetch à l'URL 'chat/new-message' avec la méthode POST
            const response = await fetch(`chat/new-message`, {
                method: 'POST', // Spécifie la méthode de la requête HTTP
                headers: { 'Content-Type': 'application/json' }, // Définit le type de contenu de la requête
                body: JSON.stringify({ roomId, message }) // Convertit l'objet JavaScript en chaîne JSON pour l'envoi
            });
            // Si la réponse n'est pas OK (statut HTTP 200-299), lance une erreur
            if (!response.ok) throw new Error('Failed to send message');
            // Renvoie la réponse convertie en JSON
            return await response.json();
            // Bloc catch pour gérer les erreurs
        } catch (error) {
            // console.error("Error sending message:", error);
            // Afficher un message d'erreur à l'utilisateur
            alert('Une erreur est survenue lors de l\'envoi du message : ' + error.message);
        }
    }

    // fonction pour afficher un message
    function displayMessage(message) {
        // Sélection de l'élément HTML avec l'ID 'messages' et stockage de sa référence dans la variable "messagesDiv"
        const messagesDiv = document.getElementById('messages');

        // Création d'un nouvel élément div et stockage de sa référence dans la variable "messageElement"
        const messageElement = document.createElement('div');

        messageElement.innerHTML = escapeHTML(message.texteMessage);
        messagesDiv.appendChild(messageElement);
    }
});
