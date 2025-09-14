<?php
header('Content-Type: application/json');
include 'conexion.php';

// Obtener CURP del POST
$curp = isset($_POST['curp']) ? $conn->real_escape_string($_POST['curp']) : '';

if (empty($curp)) {
    echo json_encode(['success' => false, 'message' => 'CURP vacía']);
    exit;
}

// Consulta SQL
$sql = "SELECT estatus FROM usuarios WHERE curp='$curp' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['estatus'] === 'activo') {
        // Guardar cookie para mantener sesión (30 días)
        setcookie('login_usuario', $curp, time() + (30 * 24 * 60 * 60), "/");
        echo json_encode(['success' => true, 'message' => 'Login exitoso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario inactivo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'CURP no encontrada']);
}

$conn->close();
?>
