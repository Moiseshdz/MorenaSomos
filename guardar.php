<?php
// ==========================================
// guardar.php — Registro jerárquico Morena
// Devuelve JSON para notificaciones flotantes
// ==========================================

header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "morenabase";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','mensaje'=>"❌ Error de conexión: ".$conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ==============================
    // 1️⃣ Datos del formulario
    // ==============================
    $curp        = strtoupper(trim($_POST['curp']));
    $nombre      = trim($_POST['nombre']);
    $apellidos   = trim($_POST['apellidos']);
    $dia         = intval($_POST['dia'] ?? 0);
    $mes         = intval($_POST['mes'] ?? 0);
    $anios       = intval($_POST['anios'] ?? 0);
    $sexo        = $_POST['sexo'] ?? '';
    $estado      = $_POST['estado'] ?? '';
    $domicilio   = $_POST['domicilio'] ?? '';
    $seccion     = $_POST['seccion'] ?? '';
    $telefono    = $_POST['telefono'] ?? '';
    $rol         = strtolower($_POST['rol'] ?? 'militante');
    $estatus     = $_POST['estatus'] ?? 'activo';

    $coordinador = ($_POST['coordinador'] !== 'Null') ? $_POST['coordinador'] : null;
    $lider       = ($_POST['lider'] !== 'Null') ? $_POST['lider'] : null;
    $sublider    = ($_POST['sublider'] !== 'Null') ? $_POST['sublider'] : null;

    // ==============================
    // 2️⃣ Subida de foto
    // ==============================
    $fotoRuta = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $dir = "uploads/";
        if (!file_exists($dir)) mkdir($dir, 0777, true);

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = $curp . "_" . time() . "." . $ext;
        $rutaDestino = $dir . $nombreArchivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
            $fotoRuta = $nombreArchivo;
        }
    }

    // ==============================
    // 3️⃣ Verificar si CURP ya existe
    // ==============================
    $tablas = ["coordinador", "lider", "sublider", "militante"];
    foreach ($tablas as $tabla) {
        $check = $conn->prepare("SELECT curp, nombre, apellidos, rol FROM $tabla WHERE curp = ? LIMIT 1");
        $check->bind_param("s", $curp);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $existe = $res->fetch_assoc();
            $relaciones = [];

            // Buscar relaciones directas según el rol
            switch ($tabla) {
                case 'militante':
                    $sql = "SELECT m.nombre AS militante, s.nombre AS sublider, l.nombre AS lider, c.nombre AS coordinador
                            FROM militante m
                            LEFT JOIN sublider s ON m.sublider = s.curp
                            LEFT JOIN lider l ON s.lider = l.curp
                            LEFT JOIN coordinador c ON l.coordinador = c.curp
                            WHERE m.curp = ?";
                    break;
                case 'sublider':
                    $sql = "SELECT s.nombre AS sublider, l.nombre AS lider, c.nombre AS coordinador
                            FROM sublider s
                            LEFT JOIN lider l ON s.lider = l.curp
                            LEFT JOIN coordinador c ON l.coordinador = c.curp
                            WHERE s.curp = ?";
                    break;
                case 'lider':
                    $sql = "SELECT l.nombre AS lider, c.nombre AS coordinador
                            FROM lider l
                            LEFT JOIN coordinador c ON l.coordinador = c.curp
                            WHERE l.curp = ?";
                    break;
                case 'coordinador':
                    $sql = "SELECT nombre AS coordinador FROM coordinador WHERE curp = ?";
                    break;
            }

            $rstmt = $conn->prepare($sql);
            $rstmt->bind_param("s", $curp);
            $rstmt->execute();
            $relRes = $rstmt->get_result();
            $relaciones = $relRes->fetch_assoc() ?: [];
            $rstmt->close();

            // Armar mensaje
            $mensaje = "⚠️ El CURP ya existe en la tabla <b>$tabla</b>.<br>";
            $mensaje .= "Registrado por: <b>" . htmlspecialchars($existe['nombre'].' '.$existe['apellidos']) . "</b><br>";
            if(!empty($relaciones)){
               # $mensaje .= "<hr><b>Relaciones directas:</b><br>";
                foreach($relaciones as $rolR => $nom){
                    if(!empty($nom)){
                        #$mensaje .= ucfirst($rolR).": ".htmlspecialchars($nom)."<br>";
                    }
                }
            }

            echo json_encode(['status'=>'error','mensaje'=>$mensaje]);
            exit;
        }
        $check->close();
    }

    // ==============================
    // 4️⃣ Insertar según el rol
    // ==============================
    switch($rol){
        case 'coordinador':
            $sql = "INSERT INTO coordinador
                    (curp, nombre, apellidos, dia, mes, anios, sexo, estado, domicilio, seccion, telefono, foto, rol, estatus)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$curp, $nombre, $apellidos, $dia, $mes, $anios, $sexo, $estado,
                       $domicilio, $seccion, $telefono, $fotoRuta, $rol, $estatus];
            break;
        case 'lider':
            $sql = "INSERT INTO lider
                    (curp, nombre, apellidos, dia, mes, anios, sexo, estado, domicilio, seccion, telefono, foto, rol, estatus, coordinador)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$curp,$nombre,$apellidos,$dia,$mes,$anios,$sexo,$estado,
                       $domicilio,$seccion,$telefono,$fotoRuta,$rol,$estatus,$coordinador];
            break;
        case 'sublider':
            $sql = "INSERT INTO sublider
                    (curp, nombre, apellidos, dia, mes, anios, sexo, estado, domicilio, seccion, telefono, foto, rol, estatus, coordinador, lider)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$curp,$nombre,$apellidos,$dia,$mes,$anios,$sexo,$estado,
                       $domicilio,$seccion,$telefono,$fotoRuta,$rol,$estatus,$coordinador,$lider];
            break;
        default: // militante
            $sql = "INSERT INTO militante
                    (curp, nombre, apellidos, dia, mes, anios, sexo, estado, domicilio, seccion, telefono, foto, rol, estatus, coordinador, lider, sublider)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$curp,$nombre,$apellidos,$dia,$mes,$anios,$sexo,$estado,
                       $domicilio,$seccion,$telefono,$fotoRuta,$rol,$estatus,$coordinador,$lider,$sublider];
            break;
    }

    // ==============================
    // 5️⃣ Ejecutar inserción
    // ==============================
    $stmt = $conn->prepare($sql);
    $tipos = str_repeat("s", count($params));
    $stmt->bind_param($tipos, ...$params);

    if($stmt->execute()){
        echo json_encode([
            'status'=>'ok',
            'mensaje'=>"✅ Registro guardado correctamente en la tabla <b>{$rol}</b>"
        ]);
    } else {
        echo json_encode([
            'status'=>'error',
            'mensaje'=>"❌ Error al guardar: ".$stmt->error
        ]);
    }

    $stmt->close();
}
$conn->close();
?>
