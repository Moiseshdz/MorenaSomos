/* ===================== Utils (utilidades) ===================== */

/**
 * getCookie(name)
 * Devuelve el valor de una cookie por nombre o undefined si no existe.
 */
function getCookie(name) {
  const matches = document.cookie.match(
    new RegExp("(?:^|; )" + name.replace(/([$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)")
  );
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

/**
 * showAlert(container, message, type, autoCloseMs)
 * Muestra una alerta simple dentro de un contenedor dado.
 */
function showAlert(container, message, type = "success", autoCloseMs = 3000) {
  container.innerHTML = "";
  const div = document.createElement("div");
  div.style.padding = "10px";
  div.style.margin = "10px 0";
  div.style.borderRadius = "8px";
  div.style.textAlign = "center";
  div.style.fontWeight = "600";
  div.style.opacity = "1";
  div.style.transition = "opacity 400ms ease";
  div.style.backgroundColor = type === "success" ? "#4CAF50" : "#611232";
  div.style.border = "1px solid #000";
  div.style.color = "#fff";
  div.style.fontFamily = 'Patria, "Noto Sans", Helvetica, Arial, sans-serif';

  div.textContent = message;
  container.appendChild(div);

  if (autoCloseMs > 0) {
    setTimeout(() => {
      div.style.opacity = "0";
      setTimeout(() => div.remove(), 600);
    }, autoCloseMs);
  }
}

/* ================= Navegación entre secciones ================= */
function mostrarSeccion(seccionId, boton = null) {
  document.querySelectorAll(".seccion").forEach((sec) => {
    sec.style.display = "none";
  });

  const target = document.getElementById(`seccion-${seccionId}`);
  if (target) target.style.display = "block";

  document.querySelectorAll(".bottom-nav button").forEach((btn) => {
    btn.classList.remove("active");
  });

  if (boton) {
    boton.classList.add("active");
  }
}

/* ===================== Inicialización ===================== */
document.addEventListener("DOMContentLoaded", () => {
  // Mostrar la sección Home por defecto
  mostrarSeccion("home", document.querySelector(".bottom-nav button"));
});

/* ================= Logout ================= */
const logoutBtn = document.getElementById("logoutBtn");
if (logoutBtn) {
  logoutBtn.addEventListener("click", () => {
    window.location.href = "logout.php";
  });
}

/* ================= Formulario por pasos ================= */
document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 0;
  const steps = document.querySelectorAll(".step");
  const progressBar = document.getElementById("progressBar");
  const notificationContainer = document.getElementById("notificationContainer");
  const form = document.getElementById("registroForm");

  // Notificación con HTML
  function showNotification(message, type = "error") {
    const notif = document.createElement("div");
    notif.innerHTML = message; // permite HTML del PHP
    notif.style.padding = "12px 18px";
    notif.style.marginBottom = "10px";
    notif.style.borderRadius = "8px";
    notif.style.fontWeight = "600";
    notif.style.color = "#fff";
    notif.style.boxShadow = "0 4px 12px rgba(0,0,0,0.2)";
    notif.style.opacity = "0";
    notif.style.transform = "translateY(-20px)";
    notif.style.transition = "all 0.5s ease";

    if (type === "success") notif.style.backgroundColor = "#28a745";
    else if (type === "warning") notif.style.backgroundColor = "#ffc107";
    else notif.style.backgroundColor = "#611232";

    notificationContainer.appendChild(notif);

    setTimeout(() => {
      notif.style.opacity = "1";
      notif.style.transform = "translateY(0)";
    }, 10);

    setTimeout(() => {
      notif.style.opacity = "0";
      notif.style.transform = "translateY(-20px)";
      setTimeout(() => notif.remove(), 600);
    }, 5000);
  }

  function showStep(n) {
    steps.forEach((step, i) => step.classList.toggle("active", i === n));
    document.getElementById("prevBtn").style.display = n === 0 ? "none" : "inline-block";
    document.getElementById("nextBtn").innerText = n === steps.length - 1 ? "Enviar" : "Siguiente";
    updateProgress(n);
  }

  function updateProgress(n) {
    const percent = ((n + 1) / steps.length) * 100;
    progressBar.style.width = percent + "%";
    const labels = ["📋 Datos personales", "📞 Contacto", "👥 Datos jerárquicos", "✅ Confirmación"];
    progressBar.setAttribute("data-text", labels[n] || "");
  }

  function validateStep() {
    const inputs = steps[currentStep].querySelectorAll("input, select, textarea");
    let allFilled = true;

    inputs.forEach((input) => {
      if (input.type !== "hidden" && !input.disabled) {
        if (!input.value.trim()) {
          input.style.border = "2px solid #dc3545";
          allFilled = false;
        } else input.style.border = "";
      }
    });

    if (!allFilled) {
      showNotification("⚠️ Por favor completa todos los campos antes de continuar.", "error");
      return false;
    }
    return true;
  }

  window.nextPrev = function (n) {
    if (n === 1 && !validateStep()) return;
    currentStep += n;

    if (currentStep >= steps.length) {
      // Último paso: enviar por AJAX
      const formData = new FormData(form);
      fetch('guardar.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
          showNotification(data.mensaje, data.status);
          if (data.status === 'ok') {
            form.reset();
            currentStep = 0;
            showStep(currentStep);
            updateProgress(currentStep);
          }
        })
        .catch(err => {
          showNotification('❌ Error en la solicitud', 'error');
          console.error(err);
        });
      return;
    }

    showStep(currentStep);
  };

  showStep(currentStep);
});



