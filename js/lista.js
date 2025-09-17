// Función para obtener cookie (misma que arriba)
function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

document.addEventListener("DOMContentLoaded", () => {
  const curpLider = getCookie("login_usuario");

  if (!curpLider) {
    console.error("No se encontró CURP del líder");
    return;
  }

  fetch("../php/lista.php?curp=" + encodeURIComponent(curpLider))
    .then(res => res.text())
    .then(data => {
      document.getElementById("lista").innerHTML = data;
    })
    .catch(err => {
      console.error("Error al cargar los registros:", err);
    });
});
