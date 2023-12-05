// document.addEventListener('DOMContentLoaded', function () {
//     var roomItems = document.querySelectorAll('.room-item');
//     const chat = document.getElementById('chat');
//     const userId = chat.dataset.userId;

//     // Envoyer le message sur appui sur la touche entrée
//     document.getElementById('message-text').addEventListener('keypress', function (e) {
//         // Vérifiez si la touche Entrée est pressée sans la touche Shift
//         if ((e.key === 'Enter' || e.keyCode === 13) && !e.shiftKey) {
//             e.preventDefault(); // Empêche l'ajout d'une nouvelle ligne dans le textarea
//             document.getElementById('send-message-form').dispatchEvent(new Event('submit', { cancelable: true }));
//         }
//     });

//     roomItems.forEach(room => {
//         room.addEventListener('click', async function (e) {

//             e.preventDefault();
//             const roomId = this.dataset.roomId;

//             // Remove class active from all rooms
//             roomItems.forEach(room => {
//                 if (room.classList.contains('room-item-active')) {
//                     room.classList.remove('room-item-active');
//                 }
//             });

//             // Add class active to the clicked room
//             this.classList.add('room-item-active');

//             let messageForm = document.getElementById('send-message-form');
//             messageForm.setAttribute('data-room-id', roomId);
//             messageForm.classList.add('show-form');

//             let messages = await getMessages(roomId)

//             // Afficher les messages
//             let messagesDiv = document.getElementById('messages');

//             messagesDiv.innerHTML = '';
//             messages.forEach(message => {
//                 let messageContentDiv = document.createElement('div');
//                 let messageContent = document.createElement('span');
//                 let pseudo = document.createElement('span');
//                 let messageRightDiv = document.createElement('div');
//                 let messageLeftDiv = document.createElement('div');
//                 let messageDate = document.createElement('span');

//                 messageContent.textContent = message.texteMessage;
//                 pseudo.textContent = message.expediteur.pseudo;

//                 const date = new Date(message.createdAt)
//                 const dateFormatted = date.toLocaleDateString("fr") + " " + date.toLocaleTimeString(["fr"], { hour: '2-digit', minute: '2-digit' });
//                 messageDate.textContent = dateFormatted;
//                 // console.log("message.expediteur.id", message.expediteur.id);
//                 // console.log("userId", userId);
//                 if (message.expediteur.id == userId) {
//                     messageContentDiv.classList.add('message-sent');
//                     pseudo.classList.add('sender-pseudo');
//                     pseudo.classList.add('sender-pseudo');

//                     messageContentDiv.appendChild(pseudo);
//                     messageContentDiv.appendChild(messageContent);
//                     messageContentDiv.appendChild(messageDate);

//                     messageRightDiv.classList.add('message-right');
//                     messageRightDiv.appendChild(messageContentDiv);
//                     messagesDiv.appendChild(messageRightDiv);
//                     messageDate.classList.add('sender-date');
//                 } else {
//                     messageContentDiv.classList.add('message-received');
//                     pseudo.classList.add('receiver-pseudo');
//                     messageDate.classList.add('receiver-date');
//                     // messageLeftDate.classList.add('receiver-date');

//                     messageContentDiv.appendChild(pseudo);
//                     messageContentDiv.appendChild(messageContent);
//                     messageContentDiv.appendChild(messageDate);

//                     messageLeftDiv.classList.add('message-left');
//                     messageLeftDiv.appendChild(messageContentDiv);

//                     messagesDiv.appendChild(messageLeftDiv);
//                 }
//             });

//             // scroll to bottom
//             messagesDiv.scrollTop = messagesDiv.scrollHeight;
//         });
//     });


//     // Envoyer un message
//     document.getElementById('send-message-form').addEventListener('submit', async function (e) {
//         e.preventDefault();
//         const roomId = this.dataset.roomId;
//         const messageText = document.getElementById('message-text').value;
//         document.getElementById('message-text').value = '';
//         // Envoyer le message au serveur
//         // Afficher le message
//         let newMessage = await sendMessage(roomId, messageText);
//         console.log("newMessage.texteMessage", newMessage.texteMessage);
//         console.log("newMessage.expediteur.pseudo", newMessage.expediteur.pseudo);

//         let messageRightDiv = document.createElement('div');
//         let messageSentDiv = document.createElement('div');
//         let spanPseudo = document.createElement('span');
//         let spanMessage = document.createElement('span');
//         let spanDate = document.createElement('span');

//         messageRightDiv.classList.add('message-right');
//         messageSentDiv.classList.add('message-sent');
//         spanPseudo.classList.add('sender-pseudo');
//         spanDate.classList.add('sender-date');
//         // spanDate.classList.add('sender-date');

//         spanPseudo.textContent = newMessage.expediteur.pseudo;
//         spanMessage.textContent = newMessage.texteMessage;
//         const date = new Date(newMessage.createdAt)
//         const dateFormatted = date.toLocaleDateString("fr") + " " + date.toLocaleTimeString(["fr"], { hour: '2-digit', minute: '2-digit' });
//         spanDate.textContent = dateFormatted;

//         messageSentDiv.appendChild(spanPseudo);
//         messageSentDiv.appendChild(spanMessage);
//         messageSentDiv.appendChild(spanDate);

//         messageRightDiv.appendChild(messageSentDiv);


//         let messagesDiv = document.getElementById('messages');
//         messagesDiv.appendChild(messageRightDiv);

//         // scroll to bottom
//         messagesDiv.scrollTop = messagesDiv.scrollHeight;

//         // update last message
//         let lastMessageDiv = document.getElementById(`last-message-${roomId}`);
//         lastMessageDiv.textContent = newMessage.texteMessage;
//         // Ne pas décommenter le code ci-dessous >>>
//         // let messages = await getMessages(roomId)
//         // let messagesDiv = document.getElementById('messages');
//         // messagesDiv.innerHTML = '';
//         // messages.forEach(message => {
//         //     let messageDiv = document.createElement('div');
//         //     let messageContent = document.createElement('span');
//         //     let senderPseudo = document.createElement('span');

//         //     messageContent.textContent = message.texteMessage;
//         //     senderPseudo.textContent = message.expediteur.pseudo;
//         //     // console.log("message.expediteur.id", message.expediteur.id);
//         //     // console.log("userId", userId);
//         //     if (message.expediteur.id == userId) {
//         //         messageDiv.classList.add('message-sent');
//         //         senderPseudo.classList.add('pseudo-sent');
//         //     } else {
//         //         messageDiv.classList.add('message-received');
//         //         senderPseudo.classList.add('pseudo-received');
//         //     }
//         //     messageDiv.appendChild(senderPseudo);
//         //     messageDiv.appendChild(messageContent);
//         //     messagesDiv.appendChild(messageDiv);
//         // });
//     });
// });


// async function getMessages(roomId) {
//     const response = await fetch(`chat/room/${roomId}`, {
//         method: 'GET',
//         headers: {
//             'Content-Type': 'application/json'
//         },
//     });
//     const data = await response.json();
//     console.log(data);
//     return data;
// }

// async function sendMessage(roomId, message) {
//     if (!roomId || !message) {
//         return;
//     }

//     if (message.trim() === '') {
//         return;
//     }
//     try {
//         console.log("roomId", roomId);
//         console.log("message", message);
//         const response = await fetch(`chat/new-message`, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json'
//             },
//             body: JSON.stringify({
//                 "roomId": roomId,
//                 "message": message
//             })

//         });
//         const result = await response.json();
//         console.log("result", result);
//         return result;
//     } catch (error) {
//         console.log("error", error);
//     }
// }


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

    //* Changer la room active
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
            if (!response.ok) throw new Error('Failed to send message');
            return await response.json();
        } catch (error) {
            console.error("Error sending message:", error);
            //* Afficher un message d'erreur à l'utilisateur
        }
    }
});
