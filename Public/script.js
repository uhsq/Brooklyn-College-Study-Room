// Get elements
const loginBtn = document.getElementById('username');

const popupContainer = document.getElementById('popupContainer');
const closeBtn = document.getElementById('closeBtn');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');

// Open popup
loginBtn.addEventListener('click', () => {
    popupContainer.style.display = 'flex';
    loginForm.classList.add('active');
    registerForm.classList.remove('active');
});




// Close popup
closeBtn.addEventListener('click', () => {
    popupContainer.style.display = 'none';
});
