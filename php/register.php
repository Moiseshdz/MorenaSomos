<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$curp = strtoupper(trim($_POST['curp'] ?? ''));
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$nacimiento = trim($_POST['nacimiento'] ?? '');
$sexo = strtoupper(trim($_POST['sexo'] ?? ''));
$estado = trim($_POST['estado'] ?? '');
$domicilio = trim($_POST['domicilio'] ?? '');
$seccion = trim($_POST['seccion'] ?? '');

if ($curp === '' || $nombre === '' || $apellido === '' || $nacimiento === '' || $sexo === '' || $estado === '' || $domicilio === '' || $seccion === '') {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

if (!preg_match('/^[A-Z0-9]{18}$/', $curp)) {
    echo json_encode(['success' => false, 'message' => 'CURP inválida']);
    exit;
}

if (!in_array($sexo, ['M', 'F'], true)) {
    echo json_encode(['success' => false, 'message' => 'Sexo inválido']);
    exit;
}

$stmt = $conn->prepare('SELECT id FROM usuarios WHERE curp = ? LIMIT 1');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la validación']);
    exit;
}

$stmt->bind_param('s', $curp);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'La CURP ya está registrada']);
    exit;
}
$stmt->close();

$fotoNombre = '';
if (!empty($_FILES['foto']['name'])) {
    $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $permitidas, true)) {
        echo json_encode(['success' => false, 'message' => 'Formato de foto no permitido']);
        exit;
    }

    if (!is_dir(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0777, true);
    }

    $fotoNombre = $curp . '.' . $ext;
    $destino = __DIR__ . '/uploads/' . $fotoNombre;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo json_encode(['success' => false, 'message' => 'Error al subir la foto']);
        exit;
    }
}

$stmt = $conn->prepare('INSERT INTO usuarios (curp, nombre, apellido, nacimiento, sexo, estado, domicilio, seccion, foto, estatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "activo")');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar el registro']);
    exit;
}

$stmt->bind_param('sssssssss', $curp, $nombre, $apellido, $nacimiento, $sexo, $estado, $domicilio, $seccion, $fotoNombre);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error en base de datos: ' . $conn->error]);
}

$stmt->close();
$conn->close();
