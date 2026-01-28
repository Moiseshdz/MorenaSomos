// Animación splash
window.addEventListener('load', () => {
    const splash = document.getElementById('splash');
    const appLogin = document.getElementById('appLogin');

    setTimeout(() => {
        splash.classList.add('hidden');
        appLogin.classList.add('visible');
    }, 2500);
});
