<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Incluir conexión
require 'conexion.php'; // este archivo crea $conn

// 1. Validar usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["ok" => false, "mensaje" => "⚠ Usuario no autenticado"]);
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// 2. Recibir datos
$curp      = $_POST['curp'] ?? null;
$nombre    = $_POST['nombre'] ?? null;
$apellido  = $_POST['apellido'] ?? null;
$dia       = $_POST['dia-nac'] ?? null;
$mes       = $_POST['mes'] ?? null;
$anio      = $_POST['anio-nac'] ?? null;
$nacimiento = "$anio-$mes-$dia";
$sexo      = $_POST['sexo'] ?? null;
$estado    = $_POST['estado'] ?? null;
$domicilio = $_POST['domicilio'] ?? null;
$seccion   = $_POST['seccion'] ?? null;

// 3. Manejo de foto
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = uniqid() . "_" . basename($_FILES['foto']['name']);
    $rutaDestino = __DIR__ . "/uploads/" . $foto;
    move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino);
}

// 4. Verificar duplicado CURP
$sql = "SELECT a.curp, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, a.registrado_en
        FROM afiliados a
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE a.curp = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $curp);
$stmt->execute();
$result = $stmt->get_result();
$afiliado = $result->fetch_assoc();

if ($afiliado) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "El afiliado con CURP {$afiliado['curp']} ya fue registrado por {$afiliado['usuario_nombre']} {$afiliado['usuario_apellido']} el {$afiliado['registrado_en']}"
    ]);
    exit;
}

// 5. Insertar nuevo afiliado
$sql = "INSERT INTO afiliados (curp, nombre, apellido, nacimiento, sexo, estado, domicilio, seccion, foto, estatus, usuario_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssi", $curp, $nombre, $apellido, $nacimiento, $sexo, $estado, $domicilio, $seccion, $foto, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "mensaje" => "Afiliado registrado correctamente"]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "❌ Error al registrar afiliado"]);
}

$stmt->close();
$conn->close();
