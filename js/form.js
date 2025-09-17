document.addEventListener("DOMContentLoaded", () => {
  const formRegistro = document.getElementById("formRegistro");

  formRegistro.innerHTML = `
    <h2 class="form-title">Registro</h2>
    <div class="progress-bar"><div class="progress"></div></div>

    <form role="form" id="registerForm" autocomplete="off" enctype="multipart/form-data">

      <!-- Paso 1: Datos básicos -->
      <div class="step active">
        <div class="form-group">
          <label for="curp">CURP</label>
          <input class="form-control" id="curp" name="curp" type="text" placeholder="XXXX000000XXXXXX00" maxlength="18" required />
        </div>
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input class="form-control" id="nombre" name="nombre" type="text" placeholder="Nombre" required />
        </div>
        <div class="form-group">
          <label for="apellidos">Apellidos</label>
          <input class="form-control" id="apellidos" name="apellidos" type="text" placeholder="Apellidos" required />
        </div>
        <button type="button" class="btn btn-primary next">Siguiente <i class="fa fa-arrow-right"></i></button>
      </div>

      <!-- Paso 2: Fecha de nacimiento -->
      <div class="step">
        <div class="form-group">
          <label for="dia">Día</label>
          <input class="form-control" id="dia" name="dia" type="number" min="1" max="31" required />
        </div>
        <div class="form-group">
          <label for="mes">Mes</label>
          <select class="form-control" id="mes" name="mes" required>
            <option value="">Selecciona un mes</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
        </div>
        <div class="form-group">
          <label for="anios">Año</label>
          <input class="form-control" id="anios" name="anios" type="number" min="1900" max="2100" required />
        </div>
        <button type="button" class="btn btn-secondary prev"><i class="fa fa-arrow-left"></i> Anterior</button>
        <button type="button" class="btn btn-primary next">Siguiente <i class="fa fa-arrow-right"></i></button>
      </div>

      <!-- Paso 3: Datos personales -->
      <div class="step">
        <div class="form-group">
          <label for="sexo">Sexo</label>
          <select class="form-control" id="sexo" name="sexo" required>
            <option value="">Selecciona el sexo</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
          </select>
        </div>
        <div class="form-group">
          <label for="estado">Estado de nacimiento</label>
          <input class="form-control" id="estado" name="estado" type="text" placeholder="Ejemplo: Chiapas" required />
        </div>
        <div class="form-group">
          <label for="telefono">Teléfono</label>
          <input class="form-control" id="telefono" name="telefono" type="text" placeholder="10 dígitos" maxlength="20" />
        </div>
        <button type="button" class="btn btn-secondary prev"><i class="fa fa-arrow-left"></i> Anterior</button>
        <button type="button" class="btn btn-primary next">Siguiente <i class="fa fa-arrow-right"></i></button>
      </div>

      <!-- Paso 4: Dirección y sección -->
      <div class="step">
        <div class="form-group">
          <label for="domicilio">Domicilio</label>
          <textarea class="form-control" id="domicilio" name="domicilio" rows="3" placeholder="Escribe tu dirección" required></textarea>
        </div>
        <div class="form-group">
          <label for="seccion">Sección</label>
          <input class="form-control" id="seccion" name="seccion" type="text" placeholder="Sección" />
        </div>
        <button type="button" class="btn btn-secondary prev"><i class="fa fa-arrow-left"></i> Anterior</button>
        <button type="button" class="btn btn-primary next">Siguiente <i class="fa fa-arrow-right"></i></button>
      </div>

      <!-- Paso 5: Foto y datos automáticos -->
      <div class="step">
        <div class="form-group">
          <label for="foto">Foto</label>
          <input type="file" class="form-control" id="foto" name="foto" accept="image/*" />
        </div>

        <div class="form-group">
          <label for="quienRegistra">CURP de quién registra</label>
          <input class="form-control" id="quienRegistra" name="quienRegistra" readonly />
        </div>

        <div class="form-group">
          <label for="relacionTexto">Coordinación</label>
          <input class="form-control" id="relacionTexto" name="relacionTexto" readonly />
        </div>

        <input type="hidden" id="relacionCoordinador" name="curp_id_coordinador" />
        <input type="hidden" id="relacionLider" name="curp_id_lider" />
        <input type="hidden" id="relacionSublider" name="curp_id_sublider" />
        <input type="hidden" id="rolNuevo" name="rol" />

        <button type="button" class="btn btn-secondary prev"><i class="fa fa-arrow-left"></i> Anterior</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i> Enviar</button>
      </div>

    </form>
  `;

  const steps = formRegistro.querySelectorAll(".step");
  const progress = formRegistro.querySelector(".progress");
  let currentStep = 0;

  function showStep(index) {
    steps.forEach((step, i) => {
      step.style.display = i === index ? "block" : "none";
    });
    progress.style.width = `${((index + 1) / steps.length) * 100}%`;
  }

  formRegistro.addEventListener("click", (e) => {
    if (e.target.closest(".next")) {
      if (currentStep < steps.length - 1) {
        currentStep++;
        showStep(currentStep);
      }
    } else if (e.target.closest(".prev")) {
      if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
      }
    }
  });

  showStep(currentStep);

  const form = document.getElementById("registerForm");
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const curpValue = formData.get("curp");
    if (curpValue) {
      formData.set("curp", curpValue.toString().toUpperCase());
    }

    fetch("../php/registro.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin"
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          alert(data.message || "Afiliado registrado correctamente");
          form.reset();
          currentStep = 0;
          showStep(currentStep);
        } else {
          alert(data.message || "No se pudo registrar al afiliado");
        }
      })
      .catch((err) => {
        console.error(err);
        alert("Error de red");
      });
  });
});
