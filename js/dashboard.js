/* ===================== Utils (utilidades) ===================== */

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

document.addEventListener("DOMContentLoaded", () => {
  const curpCookie = getCookie("login_usuario");
  if (!curpCookie) {
    window.location.href = "../index.html";
  }
});

async function cargarDatosUsuario() {
  try {
    const res = await fetch("../php/dashboard.php", { credentials: "same-origin" });
    const data = await res.json();

    const container = document.getElementById("dashboardContainer");
    const loading = document.getElementById("loading");

    if (data.success) {
      const user = data.user;
      const jerarquia = data.jerarquia || {};

      const elNombre = document.getElementById("userNombre");
      const elCurp = document.getElementById("userCURP");
      const elFoto = document.getElementById("userFoto");
      const elSubtitle = document.getElementById("subtitle");

      if (elNombre) elNombre.textContent = user.nombre;
      if (elCurp) elCurp.textContent = `CURP: ${user.curp}`;
      if (elFoto) elFoto.src = user.foto ? `../php/uploads/${user.foto}` : "../src/avatar.jpg";

      if (elSubtitle) {
        const partes = [];
        if (jerarquia.coordinador) {
          partes.push(`Coordinador - ${jerarquia.coordinador.nombre} ${jerarquia.coordinador.apellidos}`);
        }
        if (jerarquia.lider) {
          partes.push(`Líder - ${jerarquia.lider.nombre} ${jerarquia.lider.apellidos}`);
        }
        if (jerarquia.sublider) {
          partes.push(`Sublíder - ${jerarquia.sublider.nombre} ${jerarquia.sublider.apellidos}`);
        }

        if (partes.length > 0) {
          const texto = partes.join(" / ");
          elSubtitle.innerHTML = `<a href='../php/relacion.php?id=${user.curp}' id='link_relacion'>
                                    <i class="fa fa-link"></i> ${texto}
                                  </a>`;
        } else {
          elSubtitle.innerHTML = `<i class="fa fa-user"></i> Sin relación asignada`;
        }
      }

      const quienRegistra = document.getElementById("quienRegistra");
      if (quienRegistra) quienRegistra.value = user.curp;

      const inputCoord = document.getElementById("relacionCoordinador");
      const inputLider = document.getElementById("relacionLider");
      const inputSub = document.getElementById("relacionSublider");
      const inputTexto = document.getElementById("relacionTexto");
      const inputRol = document.getElementById("rolNuevo");

      if (inputCoord) inputCoord.value = jerarquia.coordinador ? jerarquia.coordinador.curp : "";
      if (inputLider) inputLider.value = jerarquia.lider ? jerarquia.lider.curp : "";
      if (inputSub) inputSub.value = jerarquia.sublider ? jerarquia.sublider.curp : "";

      if (inputTexto) {
        const textoPartes = [];
        if (jerarquia.rol) {
          textoPartes.push(`Rol actual: ${jerarquia.rol}`);
        }
        if (jerarquia.coordinador) {
          textoPartes.push(`Coord.: ${jerarquia.coordinador.curp}`);
        }
        if (jerarquia.lider) {
          textoPartes.push(`Líder: ${jerarquia.lider.curp}`);
        }
        if (jerarquia.sublider) {
          textoPartes.push(`Sublíder: ${jerarquia.sublider.curp}`);
        }
        inputTexto.value = textoPartes.length > 0 ? textoPartes.join(" | ") : "Sin relación";
      }

      if (inputRol) {
        const rolActual = (jerarquia.rol || '').toLowerCase();
        let rolSugerido = 'afiliado';
        if (rolActual === 'coordinador') {
          rolSugerido = 'lider';
        } else if (rolActual === 'lider') {
          rolSugerido = 'sublider';
        } else if (rolActual === 'sublider') {
          rolSugerido = 'afiliado';
        }
        inputRol.value = rolSugerido;
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

if (!window.__regFormHandlerBound) {
  window.__regFormHandlerBound = true;

  document.addEventListener("DOMContentLoaded", () => {
    mostrarSeccion("home", document.querySelector(".bottom-nav button"));
    cargarDatosUsuario();
  });
}
