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
                console.log("message.expediteur.id", message.expediteur.id);
                console.log("userId", userId);
                if (message.expediteur.id == userId) {
                    messageDiv.classList.add('message-sent');
                } else {
                    messageDiv.classList.add('message-received');
                }
                messageDiv.innerHTML = message.texteMessage;
                messagesDiv.appendChild(messageDiv);
            });
        });
    });


    // Envoyer un message
    document.getElementById('send-message-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const roomId = this.dataset.roomId;
        const messageText = document.getElementById('message-text').value;

        // Envoyer le message au serveur
        // sendMessage(roomId, messageText);
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

async function sendMessage(roomId, messageText) {
    const response = await fetch(`chat/room`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            "roomId": roomId,
            "message": messageText
        })

    });
    const data = await response.json();
    console.log(data);
    return data;
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