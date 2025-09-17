    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    if (!id) {
      document.getElementById("vista").innerHTML = "<p>No se especificó un afiliado.</p>";
    } else {
      fetch("../php/vista.php?id=" + id)
        .then(res => res.text())
        .then(data => {
          document.getElementById("vista").innerHTML = data;
        })
        .catch(err => {
          document.getElementById("vista").innerHTML = "<p>Error al cargar el perfil.</p>";
          console.error(err);
        });
    }