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

/* ============ Validación de sesión al cargar ============ */
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
      const jerarquia = data.jerarquia || {}; // ✅ usar jerarquia en lugar de relacion

      // Tarjeta de bienvenida
      const elNombre = document.getElementById("userNombre");
      const elCurp = document.getElementById("userCURP");
      const elFoto = document.getElementById("userFoto");
      const elSubtitle = document.getElementById("subtitle");

      if (elNombre) elNombre.textContent = user.nombre;
      if (elCurp) elCurp.textContent = `CURP: ${user.curp}`;
      if (elFoto) elFoto.src = `../php/uploads/${user.foto}`;

      // Subtítulo debajo de la tarjeta
      if (elSubtitle) {
        if (jerarquia.coordinador || jerarquia.lider || jerarquia.sublider) {
          let texto = "";
          if (jerarquia.coordinador) {
            texto = `Coordinador - ${jerarquia.coordinador.nombre} ${jerarquia.coordinador.apellidos}`;
          } else if (jerarquia.lider) {
            texto = `Líder - ${jerarquia.lider.nombre} ${jerarquia.lider.apellidos}`;
          } else if (jerarquia.sublider) {
            texto = `Sublíder - ${jerarquia.sublider.nombre} ${jerarquia.sublider.apellidos}`;
          }
          elSubtitle.innerHTML = `<a href='../php/relacion.php?id=${user.curp}' id='link_relacion'>
                                    <i class="fa fa-link"></i> ${texto}
                                  </a>`;
        } else {
          elSubtitle.innerHTML = `<i class="fa fa-user"></i> Sin relación asignada`;
        }
      }

      // En el formulario paso 5 → quién registra = su propia CURP
      const quienRegistra = document.getElementById("quienRegistra");
      if (quienRegistra) quienRegistra.value = user.curp;

      // Inputs de relaciones
      const inputLider = document.getElementById("relacionLider");
      const inputCoord = document.getElementById("relacionCoordinador");
      const inputSub = document.getElementById("relacionSublider");

      if (inputCoord) {
        inputCoord.value = jerarquia.coordinador
          ? jerarquia.coordinador.curp
          : "Sin asignar";
      }

      if (inputLider) {
        inputLider.value = jerarquia.lider
          ? jerarquia.lider.curp
          : "Sin asignar";
      }

      if (inputSub) {
        inputSub.value = jerarquia.sublider
          ? jerarquia.sublider.curp
          : "Sin asignar";
      }

      if (loading) loading.style.display = "none";
      if (container) container.style.display = "flex";
    } else {
      alert(data.message || "No hay sesión activa");
      window.location.href = "../index.html";
    }
  } catch (err) {
    console.error(err);
    window.location.href = "../index.html";
  }
}

/* ============================ Logout ============================ */
const logoutBtn = document.getElementById("logoutBtn");
if (logoutBtn) {
  logoutBtn.addEventListener("click", async () => {
    try {
      const res = await fetch("../php/logout.php", { credentials: "same-origin" });
      const data = await res.json();
      if (data.success) window.location.href = "../index.html";
    } catch (e) {
      console.error(e);
    }
  });
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
if (!window.__regFormHandlerBound) {
  window.__regFormHandlerBound = true;

  document.addEventListener("DOMContentLoaded", () => {
    mostrarSeccion("home", document.querySelector(".bottom-nav button"));
    cargarDatosUsuario();
  });
}
