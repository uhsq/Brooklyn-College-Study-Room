const registerBtn = document.getElementById('registerBtn');

registerBtn.addEventListener('click', () => {
    // take input and post to php
    const form = new FormData(document.getElementById("login-form"));
    const email = form.get("loginEmail");
    const password = form.get("loginPassword");

    let payload = {
        "email": email,
        "password": password
    };

    let url = '../PHP/Login_Page.php'; // php script that handles the information
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(res => loginPostResult(res)
            // if (!res.ok) {
            //     throw new Error('Network response was not ok');
            // }
            // return res.json();

        )
        .then(data => {
            // Handle the response data
            console.log(data);

        })
        .catch(e => {
            console.error('Error:', e);
        });

    let user_input = prompt("A One-time code was sent to your email. Please enter it below:");

    // if success, message: "Account Successfully Created!"
    // change "Login/Register" to "[Email][Down-arrow]"

    // if fail, message: "[Otp Error Message]"
});