<?php
$servername = "localhost";   // o la IP de tu servidor MySQL
$username   = "root";        // tu usuario de MySQL
$password   = "";            // tu contraseña de MySQL
$dbname     = "morenabase";  // el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
