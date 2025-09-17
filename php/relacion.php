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
            /* top, right, bottom, left */
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
            /* scroll si es necesario */
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
include 'conexion.php';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id) {
    $stmt = $conn->prepare("
        SELECT a.*, s.colonia, s.referencia
        FROM afiliados a
        LEFT JOIN secciones s ON a.seccion = s.seccion
        WHERE a.curp = ?
    ");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "<div class='perfil-app'>";
     echo '<div class="perfil-header"><h1>¡Quien te registró!</h1> <i class="fa-solid fa-chevron-down"></i></div>';


        echo "<div class='perfil-avatar'>";
        echo "<img src='../php/uploads/" . htmlspecialchars($row['foto']) . "' alt='Foto'>";
        echo "<h2>" . htmlspecialchars($row['nombre']) . " " . htmlspecialchars($row['apellidos']) . "</h2>";
        echo "<p><i class='fa fa-users'></i> " . htmlspecialchars($row['rol']) . "</p>";
        echo "</div>";

        echo "<div class='perfil-info'>";
        echo "<div class='info-item'><i class='fa fa-id-card'></i> CURP: " . htmlspecialchars($row['curp']) . "</div>";
        echo "<div class='info-item'><i class='fa fa-phone'></i> Teléfono: " . (!empty($row['telefono']) ? htmlspecialchars($row['telefono']) : "No registrado") . "</div>";
        echo "<div class='info-item'><i class='fa fa-home'></i> Domicilio: " . (!empty($row['domicilio']) ? htmlspecialchars($row['domicilio']) : "No registrado") . "</div>";

        // Mostrar sección
        echo "<div class='info-item'><i class='fa fa-map'></i> Sección: " . htmlspecialchars($row['seccion']);
        if (!empty($row['colonia']) || !empty($row['referencia'])) {
            echo " - " . htmlspecialchars($row['colonia']) . " (" . htmlspecialchars($row['referencia']) . ")";
        }
        echo "</div>";
        echo "</div>"; // perfil-info

        // Contenedor mapa
        echo "<div id='map'></div>";

        // Construir dirección dinámica
        $direccion = "Reforma, Chiapas, " . $row['colonia'];
        if (!empty($row['referencia'])) {
            $direccion .= ", " . $row['referencia'];
        }
        $direccion .= ", México";

        echo "<script>var direccion = " . json_encode($direccion) . ";</script>";

        echo "<div class='perfil-footer'>";
        echo "<button id='btnVolver' class='btn-volver'><i class='fa fa-arrow-left'></i> Volver</button>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<p style='text-align:center;color:red;'>No se encontró el afiliado con esa CURP.</p>";
    }
    $stmt->close();
}
$conn->close();
?>


</body>

</html>



<script>
document.addEventListener("DOMContentLoaded", () => {
  const btnVolver = document.getElementById("btnVolver");
  if (btnVolver) {
    btnVolver.addEventListener("click", () => {
      // Si hay historial, regresa
      if (window.history.length > 1) {
        window.history.back();
      } else {
        // Si no hay historial, redirige al dashboard
        window.location.href = "../html/dashboard.html";
      }
    });
  }
});



</script>

