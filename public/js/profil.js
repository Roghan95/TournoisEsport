document.addEventListener('DOMContentLoaded', function () {
    // let inviteBtn = document.getElementById("invite-user");
    let followBtn = document.getElementById("follow-user");

    // inviteBtn.addEventListener('click', function (e) {
    //     e.preventDefault();
    //     let userId = inviteBtn.dataset.userId;
    //     console.log('userId', userId);
    //     fetch('/api/invite-user', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json'
    //         },
    //         body: JSON.stringify({
    //             "userId": userId,
    //         })
    //     })
    //         .then(response => response.json())
    //         .then(data => {
    //             console.log('reponse:', data);
    //             let result = data.success;
    //             console.log('result', result);
    //             if (result == true) {
    //                 changeInviteBtnState();
    //             }
    //         })
    //         .catch(error => console.error('Error:', error));
    // });

    if(followBtn != null){
        followBtn.addEventListener('click', function (e) {
            e.preventDefault();
            let userId = followBtn.dataset.userId;
            console.log('userId', userId);
            fetch('/api/follow-user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    "userId": userId,
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log('reponse:', data);
                    let result = data.success;
                    let text = data.textContent;
                    console.log('result', result);
                    if (result == true) {
                        changeFollowBtnState(text);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }
    function changeFollowBtnState(text){
        followBtn.classList.add("disabled");
        followBtn.innerHTML = text;
    }
});