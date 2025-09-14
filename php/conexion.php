<?php
// Incluimos el archivo de configuración que está fuera de la carpeta pública
// Este archivo contiene los datos de conexión en un array:
$config = include('config.php');

// Crear la conexión con la base de datos usando mysqli
$conn = new mysqli(
    $config['host'],   // Servidor de la base de datos
    $config['user'],   // Usuario de la base de datos
    $config['pass'],   // Contraseña
    $config['dbname']  // Nombre de la base de datos
);

// Verificar si hubo un error en la conexión
if ($conn->connect_error) {
    // Si falla, termina la ejecución y muestra el error
    die("Conexión fallida: " . $conn->connect_error);
} else {
    // Mensaje solo para pruebas, NO dejar en producción
    //echo "Conexión Realizada";
}

// Configurar el conjunto de caracteres a UTF-8
// Esto evita problemas con acentos y caracteres especiales
$conn->set_charset("utf8");
