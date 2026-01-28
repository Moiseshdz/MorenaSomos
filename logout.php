<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la sesión en el servidor
session_destroy();

// Borrar cookie de login
if (isset($_COOKIE['login_usuario'])) {
    setcookie("login_usuario", "", time() - 3600, "/");
}

// Redirigir al login
header("Location: index.php");
exit;
?>
