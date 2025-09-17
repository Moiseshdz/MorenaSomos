<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/registro_handler.php';

$usuarioId = $_SESSION['usuario_id'] ?? null;
$usuarioCurp = $_SESSION['usuario_curp'] ?? null;

if (!$usuarioId && isset($_COOKIE['login_usuario'])) {
    $curpCookie = strtoupper(trim($_COOKIE['login_usuario']));
    if ($curpCookie !== '') {
        $stmt = $conn->prepare('SELECT id, curp FROM usuarios WHERE curp = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $curpCookie);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows === 1) {
                $fila = $res->fetch_assoc();
                $usuarioId = (int) $fila['id'];
                $usuarioCurp = $fila['curp'];
                $_SESSION['usuario_id'] = $usuarioId;
                $_SESSION['usuario_curp'] = $usuarioCurp;
            }
            $stmt->close();
        }
    }
}

if (!$usuarioId) {
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    $conn->close();
    exit;
}

$stmt = $conn->prepare('SELECT id, curp, nombre, apellido, nacimiento, sexo, estado, domicilio, seccion, foto FROM usuarios WHERE id = ? LIMIT 1');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener el usuario']);
    $conn->close();
    exit;
}

$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows !== 1) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    $conn->close();
    exit;
}

$usuario = $res->fetch_assoc();
$stmt->close();

$afiliadoPropio = obtenerAfiliadoPorCurp($conn, $usuario['curp']);

$jerarquia = [
    'rol' => $afiliadoPropio['rol'] ?? null,
    'coordinador' => obtenerResumenRelacion($conn, $afiliadoPropio['curp_id_coordinador'] ?? null),
    'lider' => obtenerResumenRelacion($conn, $afiliadoPropio['curp_id_lider'] ?? null),
    'sublider' => obtenerResumenRelacion($conn, $afiliadoPropio['curp_id_sublider'] ?? null),
];

if (!$jerarquia['rol'] && $afiliadoPropio) {
    $jerarquia['rol'] = $afiliadoPropio['rol'] ?? null;
}

$respuesta = [
    'success' => true,
    'user' => [
        'id' => (int) $usuario['id'],
        'curp' => $usuario['curp'],
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'nacimiento' => $usuario['nacimiento'],
        'sexo' => $usuario['sexo'],
        'estado' => $usuario['estado'],
        'domicilio' => $usuario['domicilio'],
        'seccion' => $usuario['seccion'],
        'foto' => $usuario['foto'] ?? '',
    ],
    'jerarquia' => $jerarquia,
];

if ($jerarquia['rol'] !== null) {
    $respuesta['relacion'] = $jerarquia; // Compatibilidad con versiones anteriores
}

echo json_encode($respuesta);
$conn->close();
