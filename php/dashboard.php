<?php
header('Content-Type: application/json');
include 'conexion.php';

$curp = $_COOKIE['login_usuario'] ?? '';

if(!$curp){
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit;
}

// Obtener datos del usuario
$sql = "SELECT nombre, apellido, foto, curp FROM usuarios WHERE curp='$curp' LIMIT 1";
$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$conn->close();
