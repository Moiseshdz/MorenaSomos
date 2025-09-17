<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil del Afiliado</title>
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style/dashboard.css?v=17" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* ===== App fullscreen ===== */
        body {
            margin: 0;
            padding: 0;
            font-family: Patria, "noso Sans", Helvetica, Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
        }

        .perfil-app {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 0;
            width: 100%;
            height: 100%;
            animation: fadeIn 0.4s ease;
        }

        .perfil-header {
            padding: 25px 15px 50px 15px;
            background: #611232;
            color: #fff;
            text-align: center;
        }

        .perfil-header h1 {
            margin: 0;
            font-size: 22px;
        }

        .perfil-avatar {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: -40px;
        }

        .perfil-avatar img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #fff;
            object-fit: cover;
            background: #eee;
        }

        .perfil-avatar h2 {
            margin: 10px 0 0;
            color: #333;
            font-size: 18px;
        }

        .perfil-avatar p {
            margin: 5px 0;
            color: #777;
            font-size: 14px;
        }

        .perfil-info {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 14px;
            margin-bottom: 12px;
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            font-size: 15px;
            color: #444;
        }

        .info-item i {
            color: #FDC745;
            margin-right: 12px;
            font-size: 18px;
        }

        .perfil-footer {
            background: #fff;
            padding: 15px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .btn-volver {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #611232, #8a1f46);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            width: 100%;
            max-width: 320px;
        }

        .btn-volver:hover {
            background: linear-gradient(135deg, #8a1f46, #611232);
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
<?php
require_once __DIR__ . '/conexion.php';

$curp = isset($_GET['id']) ? strtoupper(trim($_GET['id'])) : '';

if ($curp === '') {
    echo "<p style='text-align:center;color:red;'>No se especificó un afiliado válido.</p>";
    $conn->close();
    exit;
}

$stmt = $conn->prepare('SELECT a.*, s.colonia, s.referencia FROM afiliados a LEFT JOIN secciones s ON a.seccion = s.seccion WHERE a.curp = ? LIMIT 1');
if (!$stmt) {
    echo "<p style='text-align:center;color:red;'>No se pudo preparar la consulta.</p>";
    $conn->close();
    exit;
}

$stmt->bind_param('s', $curp);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $foto = !empty($row['foto']) ? '../php/uploads/' . htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8') : '../src/avatar.jpg';
    $nombre = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
    $apellidos = htmlspecialchars($row['apellidos'] ?? '', ENT_QUOTES, 'UTF-8');
    $rol = htmlspecialchars($row['rol'] ?? 'Sin rol', ENT_QUOTES, 'UTF-8');
    $curpTexto = htmlspecialchars($row['curp'] ?? '', ENT_QUOTES, 'UTF-8');
    $telefono = htmlspecialchars($row['telefono'] ?? 'No registrado', ENT_QUOTES, 'UTF-8');
    $domicilio = htmlspecialchars($row['domicilio'] ?? 'No registrado', ENT_QUOTES, 'UTF-8');
    $seccion = htmlspecialchars($row['seccion'] ?? 'Sin sección', ENT_QUOTES, 'UTF-8');
    $colonia = htmlspecialchars($row['colonia'] ?? '', ENT_QUOTES, 'UTF-8');
    $referencia = htmlspecialchars($row['referencia'] ?? '', ENT_QUOTES, 'UTF-8');
    $estatus = htmlspecialchars($row['estatus'] ?? 'desconocido', ENT_QUOTES, 'UTF-8');
    $registrado = htmlspecialchars($row['registrado_en'] ?? '', ENT_QUOTES, 'UTF-8');

    echo "<div class='perfil-app'>";
    echo "<div class='perfil-header'><h1>¡Quien te registró!</h1> <i class='fa-solid fa-chevron-down'></i></div>";

    echo "<div class='perfil-avatar'>";
    echo "<img src='{$foto}' alt='Foto'>";
    echo "<h2>{$nombre} {$apellidos}</h2>";
    echo "<p><i class='fa fa-users'></i> {$rol}</p>";
    echo "</div>";

    echo "<div class='perfil-info'>";
    echo "<div class='info-item'><i class='fa fa-id-card'></i> CURP: {$curpTexto}</div>";
    echo "<div class='info-item'><i class='fa fa-phone'></i> Teléfono: {$telefono}</div>";
    echo "<div class='info-item'><i class='fa fa-home'></i> Domicilio: {$domicilio}</div>";

    echo "<div class='info-item'><i class='fa fa-map'></i> Sección: {$seccion}";
    if ($colonia !== '' || $referencia !== '') {
        $detalle = trim($colonia . ' ' . ($referencia !== '' ? "({$referencia})" : ''));
        echo ' - ' . htmlspecialchars($detalle, ENT_QUOTES, 'UTF-8');
    }
    echo "</div>";

    echo "<div class='info-item'><i class='fa fa-flag'></i> Estatus: {$estatus}</div>";
    if ($registrado !== '') {
        echo "<div class='info-item'><i class='fa fa-calendar'></i> Registrado en: {$registrado}</div>";
    }
    echo "</div>"; // perfil-info

    echo "<div class='perfil-footer'>";
    echo "<button id='btnVolver' class='btn-volver'><i class='fa fa-arrow-left'></i> Volver</button>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p style='text-align:center;color:red;'>No se encontró el afiliado con esa CURP.</p>";
}

$stmt->close();
$conn->close();
?>

</body>

</html>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const btnVolver = document.getElementById("btnVolver");
  if (btnVolver) {
    btnVolver.addEventListener("click", () => {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = "../html/dashboard.html";
      }
    });
  }
});
</script>
