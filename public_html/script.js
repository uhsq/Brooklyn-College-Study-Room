// Get elements
const userBtn = document.getElementById('username');

// send get request for email session variable
let checkUser = "PHP/checkUser.php";
fetch(checkUser, {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json'
    }
}).then(data => {

    return data.json();
    
}).then(res => {
    console.log(res);
    // if it exists, change 'username' button to logout button
    // else, default login/register popup
    if (res.logged) {
        userBtn.textContent = res.email + "↓";
        logoutPopup();
    } else {
        logRegPopup();
    }
}).catch(e => {
    console.error('Error:', e);
});


function logRegPopup() {

    const popupContainer = document.getElementById('popupContainer');
    const closeBtn = document.getElementById('closeBtn');
    const loginForm = document.getElementById('loginForm');

    // Open popup
    userBtn.addEventListener('click', () => {
        popupContainer.style.display = 'flex';
        loginForm.classList.add('active');
    });

    // Close popup
    closeBtn.addEventListener('click', () => {
        popupContainer.style.display = 'none';
    });
}

function logoutPopup() {

    const popupContainer = document.getElementById('popupContainer2');
    const logoutButton = document.getElementById('logoutdiv');

    // Open popup
    userBtn.addEventListener('click', () => {
        popupContainer.style.display = 'block';
        logoutButton.classList.add('active');
    });

    // Close popup
    popupContainer.addEventListener('click', (event) => {
        if (event.target === popupContainer) {
            popupContainer.style.display = 'none';
        }
    });
}
