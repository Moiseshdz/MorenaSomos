<?php
header('Content-Type: application/json');
include 'conexion.php';

$curp = $_COOKIE['login_usuario'] ?? '';

if(!$curp){
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit;
}

$sql = "SELECT * FROM afiliados WHERE curp='$curp' LIMIT 1";
$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    $user = $result->fetch_assoc();
    $relacion = null;

    $rol = strtolower(trim($user['rol']));

    if($rol === "lider" && trim($user['curp_id_coordinador']) !== ""){
        $curpRelacion = $conn->real_escape_string($user['curp_id_coordinador']);
        $resRel = $conn->query("SELECT nombre, apellidos, curp FROM afiliados WHERE curp='$curpRelacion' LIMIT 1");
        if($resRel && $resRel->num_rows > 0){
            $relacion = $resRel->fetch_assoc();
            $relacion['tipo'] = "Coordinador";
        }
    } elseif($rol === "sublider" && trim($user['curp_id_lider']) !== ""){
        $curpRelacion = $conn->real_escape_string($user['curp_id_lider']);
        $resRel = $conn->query("SELECT nombre, apellidos, curp FROM afiliados WHERE curp='$curpRelacion' LIMIT 1");
        if($resRel && $resRel->num_rows > 0){
            $relacion = $resRel->fetch_assoc();
            $relacion['tipo'] = "Líder";
        }
    } elseif($rol === "afiliado" && trim($user['curp_id_sublider']) !== ""){
        $curpRelacion = $conn->real_escape_string($user['curp_id_sublider']);
        $resRel = $conn->query("SELECT nombre, apellidos, curp FROM afiliados WHERE curp='$curpRelacion' LIMIT 1");
        if($resRel && $resRel->num_rows > 0){
            $relacion = $resRel->fetch_assoc();
            $relacion['tipo'] = "Sublíder";
        }
    }

    echo json_encode([
        'success' => true,
        'user' => $user,
        'relacion' => $relacion
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$conn->close();
