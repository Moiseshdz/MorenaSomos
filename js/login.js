// Animación splash
window.addEventListener('load', () => {
    const splash = document.getElementById('splash');
    const appLogin = document.getElementById('appLogin');

    setTimeout(() => {
        splash.classList.add('hidden');
        appLogin.classList.add('visible');
    }, 2500);
});

// Elementos
const loginForm = document.getElementById('loginForm');
const errorMessage = document.getElementById('errorMessage');
const successMessage = document.getElementById('successMessage');

// Función para obtener cookie
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

// Revisar si ya hay cookie de sesión activa
document.addEventListener('DOMContentLoaded', () => {
    const curpCookie = getCookie('login_usuario');
    if (curpCookie) {
        successMessage.textContent = "¡Sesión activa!";
        successMessage.style.display = 'block';
        setTimeout(() => {
            window.location.replace('html/dashboard.html');
        }, 1000);
    }
});

// Manejar envío de login
loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const curp = document.getElementById('curp').value.trim();
    if (!curp) return;

    fetch('php/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `curp=${encodeURIComponent(curp)}`,
        credentials: 'same-origin'
    })
    .then(res => res.text()) // Temporal para debug
    .then(text => {
        console.log(text); // Ver qué devuelve realmente el PHP
        try {
            const data = JSON.parse(text);

            if(data.success){
                errorMessage.style.display = 'none';
                successMessage.textContent = "¡Inicio de sesión exitoso!";
                successMessage.style.display = 'block';
                setTimeout(() => {
                    window.location.replace('html/dashboard.html');
                }, 500);
            } else {
                successMessage.style.display = 'none';
                errorMessage.textContent = data.message || "CURP incorrecta o inactiva";
                errorMessage.style.display = 'block';
            }
        } catch(e){
            console.error("No es JSON válido:", e, text);
            successMessage.style.display = 'none';
            errorMessage.textContent = "Ocurrió un error al iniciar sesión";
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
