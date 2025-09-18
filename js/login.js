window.addEventListener('load', () => {
    const splash = document.getElementById('splash');
    const appLogin = document.getElementById('appLogin');

    setTimeout(() => {
        splash.classList.add('hidden');
        appLogin.classList.add('visible');
    }, 2500);
});

const loginForm = document.getElementById('loginForm');
const errorMessage = document.getElementById('errorMessage');
const successMessage = document.getElementById('successMessage');

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

document.addEventListener('DOMContentLoaded', () => {
    const curpCookie = getCookie('login_usuario');
    if (curpCookie) {
        successMessage.textContent = "¡Sesión activa!";
        successMessage.style.display = 'block';
        setTimeout(() => {
            window.location.replace('php/dashboard_app.php');
        }, 1000);
    }
});

loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const curp = document.getElementById('curp').value.trim().toUpperCase();
    if (!curp) return;

    fetch('php/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `curp=${encodeURIComponent(curp)}`,
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            errorMessage.style.display = 'none';
            successMessage.textContent = data.message || "¡Inicio de sesión exitoso!";
            successMessage.style.display = 'block';
            setTimeout(() => {
                window.location.replace('php/dashboard_app.php');
            }, 500);
        } else {
            successMessage.style.display = 'none';
            errorMessage.textContent = data.message || "CURP incorrecta o inactiva";
            errorMessage.style.display = 'block';
        }
    })
    .catch(err => {
        console.error(err);
        successMessage.style.display = 'none';
        errorMessage.textContent = "Ocurrió un error al iniciar sesión";
        errorMessage.style.display = 'block';
    });
});
