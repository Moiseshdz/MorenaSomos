<?php
session_start();
if (isset($_SESSION['usuario_id']) || !empty($_COOKIE['login_usuario'])) {
    header('Location: dashboard_app.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - App Morena</title>

  <!-- Framework gob.mx -->
  <link rel="stylesheet" href="https://framework-gb.cdn.gob.mx/assets/styles/main.css">
  <link rel="stylesheet" href="../style/registar.css?v=2">
   <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet">

</head>

<body>
  <!-- Registro full screen -->
  <div class="app-login">
    <div class="logo-container">
      <img src="../src/logo.png" alt="Morena Logo">
      <h2>¡Regístrate!</h2>
      <p class="subtitle">Crea tu cuenta para acceder</p>
    </div>

    <!-- Mensajes de alerta -->
    <div class="alert error" id="registerError"></div>
    <div class="alert success" id="registerSuccess"></div>

    <!-- Formulario de registro -->
    <form id="registerForm" enctype="multipart/form-data">
      <div class="form-group">
        <input type="text" name="curp" class="form-control" placeholder="CURP" required>
      </div>
      <div class="form-group">
        <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
      </div>
      <div class="form-group">
        <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
      </div>
      <div class="form-group">
        <input type="date" name="nacimiento" class="form-control" placeholder="Fecha de Nacimiento" required>
      </div>
      <div class="form-group">
        <select name="sexo" class="form-control" required>
          <option value="">Selecciona Sexo</option>
          <option value="M">Masculino</option>
          <option value="F">Femenino</option>
        </select>
      </div>
      <div class="form-group">
        <input type="text" name="estado" class="form-control" placeholder="Estado" required>
      </div>
      <div class="form-group">
        <input type="text" name="domicilio" class="form-control" placeholder="Domicilio" required>
      </div>
      <div class="form-group">
        <input type="text" name="seccion" class="form-control" placeholder="Sección" required>
      </div>
      <div class="form-group">
        <input type="file" name="foto" class="form-control" accept="image/*" required>
      </div>
      <button type="submit" class="btn-primary">Crear Cuenta</button>
    </form>


    <div class="forgot-password">
      <a href="../index.php">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
  </div>
</body>
<script src="../js/registrer.js?v=1"></script>
</html>
