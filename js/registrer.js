

const form = document.getElementById('registerForm');
const registerError = document.getElementById('registerError');
const registerSuccess = document.getElementById('registerSuccess');

form.addEventListener('submit', function(e) {
    e.preventDefault(); // Evita que la página se recargue

    // Limpiar alertas
    registerError.style.display = 'none';
    registerSuccess.style.display = 'none';
    registerError.textContent = '';
    registerSuccess.textContent = '';

    const formData = new FormData(form); // Captura todos los datos incluyendo la foto

    fetch('../php/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            registerSuccess.textContent = data.message;
            registerSuccess.style.display = 'block';
            form.reset(); // Limpiar formulario
        } else {
            registerError.textContent = data.message;
            registerError.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        registerError.textContent = 'Ocurrió un error en el registro.';
        registerError.style.display = 'block';
    });
});

