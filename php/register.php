<?php
header('Content-Type: application/json');
include 'conexion.php';

// Recibir y limpiar datos del formulario
$curp = $conn->real_escape_string($_POST['curp'] ?? '');
$nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
$apellido = $conn->real_escape_string($_POST['apellido'] ?? '');
$nacimiento = $conn->real_escape_string($_POST['nacimiento'] ?? '');
$sexo = $conn->real_escape_string($_POST['sexo'] ?? '');
$estado = $conn->real_escape_string($_POST['estado'] ?? '');
$domicilio = $conn->real_escape_string($_POST['domicilio'] ?? '');
$seccion = $conn->real_escape_string($_POST['seccion'] ?? '');
$foto = '';

// Validación de campos obligatorios
if(empty($curp) || empty($nombre) || empty($apellido) || empty($nacimiento) || empty($sexo) || empty($estado) || empty($domicilio) || empty($seccion)){
    echo json_encode(['success'=>false, 'message'=>'Faltan datos obligatorios']);
    exit;
}

// Verificar si la CURP ya existe
$check = $conn->query("SELECT curp FROM usuarios WHERE curp='$curp' LIMIT 1");
if($check->num_rows > 0){
    echo json_encode(['success'=>false, 'message'=>'La CURP ya está registrada']);
    exit;
}

// Subida de foto usando la CURP como nombre
if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0){
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $foto = $curp . '.' . $ext; // La foto se nombra con la CURP
    $uploadDir = 'uploads/';
    
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    if(!move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $foto)){
        echo json_encode(['success'=>false, 'message'=>'Error al subir la foto']);
        exit;
    }
} else {
    echo json_encode(['success'=>false, 'message'=>'Debe subir una foto']);
    exit;
}

// Insertar en la base de datos
$sql = "INSERT INTO usuarios (curp, nombre, apellido, nacimiento, sexo, estado, domicilio, seccion, foto, estatus)
        VALUES ('$curp','$nombre','$apellido','$nacimiento','$sexo','$estado','$domicilio','$seccion','$foto','activo')";

if($conn->query($sql) === TRUE){
    echo json_encode(['success'=>true,'message'=>'Usuario registrado correctamente']);
} else {
    echo json_encode(['success'=>false,'message'=>'Error en base de datos: '.$conn->error]);
}

$conn->close();
