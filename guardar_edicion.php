<?php
session_start();
include 'config/conexion.php';

// Establecer zona horaria de México
$conn->query("SET time_zone = 'America/Mexico_City'");

// Leer JSON recibido
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'], $input['tabla'], $input['datos'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Datos incompletos']);
    exit;
}

$id = intval($input['id']);
$tabla = preg_replace('/[^a-z_]/i', '', $input['tabla']); // seguridad
$datos = $input['datos'];

// === Verificar si el CURP ya existe y quién lo registró ===
if (isset($datos['curp']) && !empty($datos['curp'])) {
    $curp = trim($datos['curp']);
    $tablas = ['coordinador', 'lider', 'sublider', 'militante'];
    foreach ($tablas as $t) {
        $sql = "SELECT nombre, apellidos, '$t' AS rol_tabla FROM $t WHERE curp = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $curp);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $registrador = $res->fetch_assoc();
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Este CURP ya está registrado.',
                'registrado_por' => [
                    'nombre' => $registrador['nombre'] . ' ' . $registrador['apellidos'],
                    'rol' => ucfirst($registrador['rol_tabla'])
                ]
            ]);
            exit;
        }
    }
}

// === Agregar timestamp de modificación ===
$datos['modificado_el'] = date("Y-m-d H:i:s");

// Construir query dinámicamente
$campos = [];
$valores = [];
foreach ($datos as $col => $val) {
    $col = preg_replace('/[^a-z_]/i','',$col); // seguridad columna
    $campos[] = "$col=?";
    $valores[] = $val;
}

$sql = "UPDATE $tabla SET " . implode(', ', $campos) . " WHERE id=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status'=>'error','mensaje'=>$conn->error]);
    exit;
}

// Tipos: todos string excepto ID que es int
$tipos = str_repeat('s', count($valores)) . 'i';
$valores[] = $id;

// Ejecutar
$stmt->bind_param($tipos, ...$valores);

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok', 'mensaje'=>'Datos actualizados correctamente']);
} else {
    echo json_encode(['status'=>'error', 'mensaje'=>$stmt->error]);
}
?>
