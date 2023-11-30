document.addEventListener('DOMContentLoaded', function () {
    var roomItems = document.querySelectorAll('.room-item');
    const chat = document.getElementById('chat');
    const userId = chat.dataset.userId;

    roomItems.forEach(room => {
        room.addEventListener('click', async function (e) {
            e.preventDefault();
            const roomId = this.dataset.roomId;

            document.getElementById('send-message-form').setAttribute('data-room-id', roomId);

            let messages = await getMessages(roomId)

            // Afficher les messages
            let messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';
            messages.forEach(message => {
                let messageDiv = document.createElement('div');
                let messageContent = document.createElement('span');
                let senderPseudo = document.createElement('span');

                messageContent.textContent = message.texteMessage;
                senderPseudo.textContent = message.expediteur.pseudo;
                // console.log("message.expediteur.id", message.expediteur.id);
                // console.log("userId", userId);
                if (message.expediteur.id == userId) {
                    messageDiv.classList.add('message-sent');
                    senderPseudo.classList.add('message-sent', 'sender-pseudo');
                } else {
                    messageDiv.classList.add('message-received');
                    senderPseudo.classList.add('message-received', 'receiver-pseudo');
                }
                messageDiv.appendChild(senderPseudo);
                messageDiv.appendChild(messageContent);
                messagesDiv.appendChild(messageDiv);
            });
        });
    });


    // Envoyer un message
    document.getElementById('send-message-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const roomId = this.dataset.roomId;
        const messageText = document.getElementById('message-text').value;
        document.getElementById('message-text').value = '';
        // Envoyer le message au serveur
        // Afficher le message
        sendMessage(roomId, messageText);
        let messages = await getMessages(roomId)
        let messagesDiv = document.getElementById('messages');
        messagesDiv.innerHTML = '';
        messages.forEach(message => {
            let messageDiv = document.createElement('div');
            let messageContent = document.createElement('span');
            let senderPseudo = document.createElement('span');

            messageContent.textContent = message.texteMessage;
            senderPseudo.textContent = message.expediteur.pseudo;
            // console.log("message.expediteur.id", message.expediteur.id);
            // console.log("userId", userId);
            if (message.expediteur.id == userId) {
                messageDiv.classList.add('message-sent');
                senderPseudo.classList.add('pseudo-sent');
            } else {
                messageDiv.classList.add('message-received');
                senderPseudo.classList.add('pseudo-received');
            }
            messageDiv.appendChild(senderPseudo);
            messageDiv.appendChild(messageContent);
            messagesDiv.appendChild(messageDiv);
        });
    });
});


async function getMessages(roomId) {
    const response = await fetch(`chat/room/${roomId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
    });
    const data = await response.json();
    console.log(data);
    return data;
}

async function sendMessage(roomId, message) {
    try {
        console.log("roomId", roomId);
        console.log("message", message);
        const response = await fetch(`chat/new-message`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "roomId": roomId,
                "message": message
            })

        });
        const result = await response.json();
        console.log(result);
        return result;
    } catch (error) {
        console.log("error", error);
    }
}


// formulaire pour envoyer un message
// let form = document.getElementById('form-message');
// form.addEventListener('submit', function (e) {
//     e.preventDefault();
//     let message = document.getElementById('message').value;
//     fetch('chat/send-message', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({
//             "roomId": roomId,
//             "message": message
//         })
//     })
//         .then(response => response.json())
//         .then(data => {
//             console.log('reponse:', data);
//             let result = data.success;
//             console.log('result', result);
//             // Traiter la réponse
//         })
//         .catch(error => console.error('Error:', error));
// });