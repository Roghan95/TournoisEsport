//* Code Optimisé 
document.addEventListener('DOMContentLoaded', function () {
    const chat = document.getElementById('chat');
    const userId = chat.dataset.userId;
    const messageTextElem = document.getElementById('message-text');
    const sendMessageForm = document.getElementById('send-message-form');
    const messagesDiv = document.getElementById('messages');
    const roomItems = document.querySelectorAll('.room-item');

    messageTextElem.addEventListener('keypress', handleKeyPress);
    sendMessageForm.addEventListener('submit', handleSubmit);
    roomItems.forEach(room => room.addEventListener('click', handleRoomClick));

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
        setActiveRoom(roomId);
        const messages = await getMessages(roomId);
        displayMessages(messages, userId);
    }

    function setActiveRoom(roomId) {
        roomItems.forEach(room => {
            room.classList.toggle('room-item-active', room.dataset.roomId === roomId);
        });
        sendMessageForm.setAttribute('data-room-id', roomId);
        sendMessageForm.classList.add('show-form');
    }

    function displayMessages(messages, userId) {
        messagesDiv.innerHTML = '';
        messages.forEach(message => appendMessage(message, userId));
        scrollToBottom();
    }

    //* Afficher un message dans la room active
    function appendMessage(message, userId) {
        let messageWrapperDiv = document.createElement('div');
        let messageContentDiv = document.createElement('div');
        let pseudoSpan = document.createElement('span');
        let textSpan = document.createElement('span');
        let dateSpan = document.createElement('span');

        pseudoSpan.textContent = message.expediteur.pseudo;
        textSpan.textContent = message.texteMessage;
        dateSpan.textContent = new Date(message.createdAt).toLocaleDateString("fr", { hour: '2-digit', minute: '2-digit' });

        if (message.expediteur.id == userId) {
            //* Message envoyé par l'utilisateur actuel
            messageWrapperDiv.classList.add('message-right');
            messageContentDiv.classList.add('message-sent');
            pseudoSpan.classList.add('sender-pseudo');
            dateSpan.classList.add('sender-date');
        } else {
            //* Message reçu d'un autre utilisateur
            messageWrapperDiv.classList.add('message-left');
            messageContentDiv.classList.add('message-received');
            pseudoSpan.classList.add('receiver-pseudo');
            dateSpan.classList.add('receiver-date');
        }

        messageContentDiv.appendChild(pseudoSpan);
        messageContentDiv.appendChild(textSpan);
        messageContentDiv.appendChild(dateSpan);
        messageWrapperDiv.appendChild(messageContentDiv);

        let messagesDiv = document.getElementById('messages');
        messagesDiv.appendChild(messageWrapperDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    //* Faire défiler vers le bas
    function scrollToBottom() {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    //* Requête GET pour récupérer les messages d'un room
    async function getMessages(roomId) {
        try {
            const response = await fetch(`chat/room/${roomId}`, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            if (!response.ok) throw new Error('Failed to fetch messages');
            return await response.json();
        } catch (error) {
            console.error("Error fetching messages:", error);
            //* Afficher un message d'erreur à l'utilisateur
        }
    }

    //* Requête POST pour envoyer un message
    async function sendMessage(roomId, message) {
        try {
            const response = await fetch(`chat/new-message`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ roomId, message })
            });
            console.log(response);
            if (!response.ok) throw new Error('Failed to send message');
            return await response.json();
        } catch (error) {
            console.error("Error sending message:", error);
            //* Afficher un message d'erreur à l'utilisateur
        }
    }

    //const eventSource = new EventSource("{{ mercure('localhost/.well-known/mercure?topic=http://exemple.com/rooms/1')|escape('js') }}");
    //eventSource.onmessage = event => {
     //   const data = JSON.parse(event.data);
      //  console.log(data);
    //};

    roomItems.forEach((room) => {
        const roomId = room.dataset.roomId;
        //const eventSource = new EventSource(`chat/${roomId}`);
        const url = new URL('http://localhost/.well-known/mercure');
        url.searchParams.append('topic', 'chat' + roomId);

        const eventSource = new EventSource(url);
        eventSource.onmessage = event => {
            const data = JSON.parse(event.data);
            console.log(data);
            
            if (data.message && data.message.destinataire.id === userId) {
                displayMessage(data.message);
            }
        };
    });

    function displayMessage(message) {
        // Logique pour afficher le message dans l'interface utilisateur
        // Par exemple, ajouter le message à la liste des messages dans la div 'messages'
        const messagesDiv = document.getElementById('messages');
        const messageElement = document.createElement('div');
        messageElement.textContent = message.texteMessage; // Assurez-vous de traiter les données pour éviter les failles XSS
        messagesDiv.appendChild(messageElement);
    }
});
