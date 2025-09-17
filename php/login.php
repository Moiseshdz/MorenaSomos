<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$curp = strtoupper(trim($_POST['curp'] ?? ''));
if ($curp === '') {
    echo json_encode(['success' => false, 'message' => 'CURP vacía']);
    exit;
}

$stmt = $conn->prepare('SELECT id, curp, nombre, apellido, foto, estatus FROM usuarios WHERE curp = ? LIMIT 1');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    exit;
}

$stmt->bind_param('s', $curp);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (strtolower($user['estatus'] ?? '') !== 'activo') {
        echo json_encode(['success' => false, 'message' => 'Usuario inactivo']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $_SESSION['usuario_id'] = (int) $user['id'];
    $_SESSION['usuario_curp'] = $user['curp'];

    setcookie(
        'login_usuario',
        $user['curp'],
        [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => false,
            'httponly' => false,
            'samesite' => 'Lax',
        ]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Login exitoso',
        'user' => [
            'id' => (int) $user['id'],
            'curp' => $user['curp'],
            'nombre' => $user['nombre'],
            'apellido' => $user['apellido'],
            'foto' => $user['foto'] ?? '',
        ],
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'CURP no encontrada']);
}

$stmt->close();
$conn->close();
