// public/assets/js/login.js

function setupPasswordToggle(inputId, iconId) {
    const input = document.querySelector(`#${inputId}`);
    const icon = document.querySelector(`#${iconId}`);

    if (input && icon) {
        icon.addEventListener('click', function () {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
}

setupPasswordToggle('loginPass', 'togglePass');
setupPasswordToggle('regPass', 'toggleRegPass');
setupPasswordToggle('regConfirmPass', 'toggleRegConfirmPass');

const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const showRegister = document.getElementById('showRegister');
const showLogin = document.getElementById('showLogin');

if (showRegister && loginForm && registerForm) {
    showRegister.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.classList.add('d-none');
        registerForm.classList.remove('d-none');
    });
}

if (showLogin && loginForm && registerForm) {
    showLogin.addEventListener('click', (e) => {
        e.preventDefault();
        registerForm.classList.add('d-none');
        loginForm.classList.remove('d-none');
    });
}