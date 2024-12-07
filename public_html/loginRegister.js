const registerBtn = document.getElementById('registerBtn');
const loginBtn = document.getElementById('loginBtn');

loginBtn.addEventListener('click', () => {

    postInfo('PHP/handleLogin.php', loginCall, logError);
});

registerBtn.addEventListener('click', () => {


    postInfo('PHP/handleRegister.php', regCall, logError);

});

function postInfo(url, callback, fallback) {

    const form = new FormData(document.getElementById("login-form"));
    const email = form.get("loginEmail");
    const password = form.get("loginPassword");


    fetch(url, { // php script that handles the information
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ // payload
            "email": email,
            "password": password
        })
    }).then(data => {
        return data.json();

    }).then(res => {
        callback(res, email);
    }).catch(e => {
        fallback(e);
    });

}

function regCall(data, email) {
    let user_input = prompt("A One-time code was sent to your email. Please enter it below:");
    let handleOTP = "PHP/handleOTP.php";

    //verify the otp
    //set otp to null in db
    //reload page
    fetch(handleOTP, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ // payload
            "email": email,
            "otp": user_input
        })
    }).then(result => {

        return result.json();

    }).then(_data => {
        console.log(_data);
        // reload page to show user logged in
        if (_data.success) {
            window.location.reload();
        }
    }).catch(e => {
        logError(e);
    });
}

function loginCall(data, email) {
    console.log(data);
    console.log(email);
    if (data.success) {
        window.location.reload();
    }
}

function logError(e) {
    console.error('Error:', e);
}