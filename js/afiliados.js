document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    const alertContainer = document.getElementById('alertContainer');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const fd = new FormData(form);
        fd.append("afiliado", "1");

        try {
            const res = await fetch('../php/afiliados_crear.php', {
                method: 'POST',
                body: fd
            });

            const text = await res.text();
            console.log("Respuesta cruda del servidor:", text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert("El servidor no devolvió JSON válido. Revisa la consola.");
                return;
            }

            // Limpiamos alertas previas
            alertContainer.innerHTML = "";

            // Crear alerta
            const alertDiv = document.createElement('div');
            alertDiv.style.padding = "10px";
            alertDiv.style.margin = "10px 0";
            alertDiv.style.borderRadius = "5px";
            alertDiv.style.textAlign = "center";
            alertDiv.style.fontWeight = "bold";

            if (data.ok) {
                alertDiv.style.backgroundColor = "#4CAF50"; // verde
                alertDiv.style.color = "white";
                alertDiv.textContent = data.mensaje;

                form.reset();
                document.getElementById('photoPreview').style.display = 'none';
            } else {
                alertDiv.style.backgroundColor = "#f44336"; // rojo
                alertDiv.style.color = "white";
                alertDiv.textContent = data.mensaje;
            }

            alertContainer.appendChild(alertDiv);

            // 🔥 Eliminar alerta automáticamente en 3 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);

        } catch (err) {
            console.error("Error en fetch:", err);
            alert("Error en la red o en el servidor");
        }
    });
});
