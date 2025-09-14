"use strict";

/* ===================== Utils ===================== */
function getCookie(name) {
  const matches = document.cookie.match(
    new RegExp("(?:^|; )" + name.replace(/([$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)")
  );
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

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
  div.style.backgroundColor = type === "success" ? "#4CAF50" : "#f44336";
  div.style.color = "#fff";
  div.textContent = message;
  container.appendChild(div);

  if (autoCloseMs > 0) {
    setTimeout(() => {
      div.style.opacity = "0";
      setTimeout(() => div.remove(), 400);
    }, autoCloseMs);
  }
}

function clearForm() {
  const form = document.getElementById("registerForm");
  const photoPreview = document.getElementById("photoPreview");
  const cameraInput = document.getElementById("cameraInput");
  const selectMes = document.getElementById("mes");
  const selectSexo = document.getElementById("sexo");
  const selectEstado = document.getElementById("estado");

  if (form) form.reset();
  if (photoPreview) { photoPreview.src = ""; photoPreview.style.display = "none"; }
  if (cameraInput) cameraInput.value = "";

  if (selectMes) selectMes.selectedIndex = 0;
  if (selectSexo) selectSexo.selectedIndex = 0;
  if (selectEstado) selectEstado.selectedIndex = 0;
}

/* ===================== Validar sesión al cargar ===================== */
document.addEventListener("DOMContentLoaded", () => {
  const curpCookie = getCookie("login_usuario");
  if (!curpCookie) {
    window.location.href = "../index.html";
  }
});

/* ===================== Cargar datos del usuario ===================== */
async function cargarDatosUsuario() {
  try {
    const res = await fetch("../php/dashboard.php");
    const data = await res.json();
    const container = document.getElementById("dashboardContainer");
    const loading = document.getElementById("loading");

    if (data.success) {
      const user = data.user;

      const elNombre = document.getElementById("userNombre");
      const elCurp = document.getElementById("userCURP");
      const elFoto = document.getElementById("userFoto");
      if (elNombre) elNombre.textContent = user.nombre;
      if (elCurp) elCurp.textContent = `CURP: ${user.curp}`;
      if (elFoto) elFoto.src = `../php/uploads/${user.foto}`;

      const relacion = document.getElementById("relacion");
      if (relacion) relacion.value = user.curp;

      if (loading) loading.style.display = "none";
      if (container) container.style.display = "flex";
    } else {
      alert(data.message || "No hay sesión activa");
      window.location.href = "../index.html";
    }
  } catch (err) {
    console.error(err);
    alert("Error al cargar datos del usuario");
    window.location.href = "../index.html";
  }
}

/* ===================== Logout ===================== */
const logoutBtn = document.getElementById("logoutBtn");
if (logoutBtn) {
  logoutBtn.addEventListener("click", async () => {
    try {
      const res = await fetch("../php/logout.php", { credentials: "same-origin" });
      const data = await res.json();
      if (data.success) window.location.href = "../index.html";
    } catch (e) { console.error(e); }
  });
}

/* ===================== Mostrar secciones ===================== */
function mostrarSeccion(seccion) {
  document.querySelectorAll(".seccion").forEach((s) => (s.style.display = "none"));
  const target = document.getElementById(`seccion-${seccion}`);
  if (target) target.style.display = "block";
}

/* ===================== App Init (solo una vez) ===================== */
if (!window.__regFormHandlerBound) {
  window.__regFormHandlerBound = true;

  document.addEventListener("DOMContentLoaded", () => {
    // Sección inicial
    mostrarSeccion("registro");

    // Cargar usuario
    cargarDatosUsuario();

    // --------- Selects dinámicos ---------
    const selectMes = document.getElementById("mes");
    if (selectMes) {
      const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
      ];
      selectMes.innerHTML = `<option value="" disabled selected>Selecciona un mes</option>`;
      meses.forEach((mes, index) => {
        const option = document.createElement("option");
        option.value = String(index + 1).padStart(2, "0"); // 01..12
        option.textContent = mes;
        selectMes.appendChild(option);
      });
    }

    const selectSexo = document.getElementById("sexo");
    if (selectSexo) {
      const sexos = [
        { value: "M", label: "Masculino" },
        { value: "F", label: "Femenino" }
      ];
      selectSexo.innerHTML = `<option value="" disabled selected>Selecciona el sexo</option>`;
      sexos.forEach(({ value, label }) => {
        const option = document.createElement("option");
        option.value = value;
        option.textContent = label;
        selectSexo.appendChild(option);
      });
    }

    const selectEstado = document.getElementById("estado");
    if (selectEstado) {
      const estados = [
        { code: "AGU", name: "Aguascalientes" },
        { code: "BCN", name: "Baja California" },
        { code: "BCS", name: "Baja California Sur" },
        { code: "CAM", name: "Campeche" },
        { code: "CHP", name: "Chiapas" },
        { code: "CHH", name: "Chihuahua" },
        { code: "COA", name: "Coahuila" },
        { code: "COL", name: "Colima" },
        { code: "CDMX", name: "Ciudad de México" },
        { code: "DUR", name: "Durango" },
        { code: "GUA", name: "Guanajuato" },
        { code: "GRO", name: "Guerrero" },
        { code: "HID", name: "Hidalgo" },
        { code: "JAL", name: "Jalisco" },
        { code: "MEX", name: "Estado de México" },
        { code: "MIC", name: "Michoacán" },
        { code: "MOR", name: "Morelos" },
        { code: "NAY", name: "Nayarit" },
        { code: "NLE", name: "Nuevo León" },
        { code: "OAX", name: "Oaxaca" },
        { code: "PUE", name: "Puebla" },
        { code: "QUE", name: "Querétaro" },
        { code: "ROO", name: "Quintana Roo" },
        { code: "SLP", name: "San Luis Potosí" },
        { code: "SIN", name: "Sinaloa" },
        { code: "SON", name: "Sonora" },
        { code: "TAB", name: "Tabasco" },
        { code: "TAM", name: "Tamaulipas" },
        { code: "TLA", name: "Tlaxcala" },
        { code: "VER", name: "Veracruz" },
        { code: "YUC", name: "Yucatán" },
        { code: "ZAC", name: "Zacatecas" }
      ];
      selectEstado.innerHTML = `<option value="" disabled selected>Selecciona un estado</option>`;
      estados.forEach(({ code, name }) => {
        const option = document.createElement("option");
        option.value = code;
        option.textContent = name;
        selectEstado.appendChild(option);
      });
    }

    // --------- Envío AJAX con anti-doble-submit ---------
    const form = document.getElementById("registerForm");
    const alertContainer = document.getElementById("alertContainer") || document.body;
    let isSubmitting = false;

    if (form) {
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;

        const submitBtn = form.querySelector('button[type="submit"]');
        const prevText = submitBtn ? submitBtn.textContent : null;
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = "Enviando..."; }

        try {
          const fd = new FormData(form);
          fd.append("afiliado", "1");

          const res = await fetch("../php/afiliados_crear.php", { method: "POST", body: fd });
          const text = await res.text();
          let data;
          try { data = JSON.parse(text); }
          catch { showAlert(alertContainer, "El servidor no devolvió JSON válido.", "error"); return; }

          if (data.ok) {
            showAlert(alertContainer, data.mensaje || "Registro exitoso", "success", 3000);
            clearForm();
          } else {
            showAlert(alertContainer, data.mensaje || "No se pudo registrar", "error", 3000);
            clearForm();
          }
        } catch (err) {
          console.error("Error en fetch:", err);
          showAlert(alertContainer, "Error en la red o en el servidor", "error", 3000);
        } finally {
          if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = prevText || "Enviar"; }
          setTimeout(() => { isSubmitting = false; }, 300);
        }
      });
    }
  });
}
