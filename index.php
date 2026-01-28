<?php
session_start();
include 'config/conexion.php';

$error = ""; // para mostrar mensajes

// 1. Si ya existe sesión → ir directo al dashboard
if (isset($_SESSION['curp'])) {
    header("Location: dashboard.php");
    exit;
}

// 2. Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['curp'])) {
    $curp = trim($_POST['curp']);
    $tablas = ['coordinador','lider','sublider','militante'];

    foreach ($tablas as $tabla) {
        $sql = "SELECT curp, nombre, apellidos, rol, estatus FROM $tabla WHERE curp=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $curp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['estatus'] === 'activo') {
                // guardar sesión
                $_SESSION['curp']      = $row['curp'];
                $_SESSION['nombre']    = $row['nombre'];
                $_SESSION['apellidos'] = $row['apellidos'];
                $_SESSION['rol']       = $row['rol'];

                // guardar cookie (ejemplo: dura 7 días)
                setcookie("login_usuario", $row['curp'], time() + (86400 * 7), "/");

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Usuario inactivo.";
            }
        }
    }

    if (empty($error)) {
        $error = "CURP no encontrada.";
    }
}

// 3. Restaurar sesión con cookie
if (!isset($_SESSION['curp']) && isset($_COOKIE['login_usuario'])) {
    $curp = $_COOKIE['login_usuario'];
    $tablas = ['coordinador','lider','sublider','militante'];

    foreach ($tablas as $tabla) {
        $sql = "SELECT curp, nombre, apellidos, rol, estatus FROM $tabla WHERE curp=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $curp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['estatus'] === 'activo') {
                $_SESSION['curp']      = $row['curp'];
                $_SESSION['nombre']    = $row['nombre'];
                $_SESSION['apellidos'] = $row['apellidos'];
                $_SESSION['rol']       = $row['rol'];
                header("Location: dashboard.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login App Morena</title>
    <link rel="stylesheet" href="https://framework-gb.cdn.gob.mx/assets/styles/main.css">
    <link rel="stylesheet" href="style/index.css?v=2">
</head>

<body>
    <!-- SPLASH SCREEN -->
    <div class="splash" id="splash">
        <img src="src/logo.png" width="100%" alt="Morena Logo">
    </div>



    <div class="app-login" id="appLogin">
        <div class="logo-container">
            <img src="src/logo.png" style="width: 50%;" alt="Morena Logo">
            <h2>¡Bienvenido!</h2>
            <p class="subtitle">Inicia sesión con tu CURP</p>
        </div>

        <!-- Mensaje de error -->
        <?php if (!empty($error)) { ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php } ?>

        <!-- Formulario de login -->
        <form method="POST" action="index.php" id="loginForm">
            <div class="form-group">
                <input type="text" name="curp" id="curp" class="form-control" placeholder="CURP" required>
            </div>
            <button type="submit" name="BtnLogin" class="btn-primary">Iniciar Sesión</button>
        </form>

        <div class="forgot-password">
            <a href="html/registrar.html">¡Regístrate!</a>
        </div>
    </div>

    <script src="js/index.js?v=500 "></script>
</body>
</html>