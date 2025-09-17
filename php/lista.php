<?php
// Incluimos la conexión
include 'conexion.php';

// Recibir la CURP del usuario logueado
$curp_usuario = isset($_GET['curp']) ? $_GET['curp'] : null;

if (!$curp_usuario) {
    die("<p>No se recibió CURP del usuario</p>");
}

// Obtenemos el rol del usuario logueado
$sqlRol = "SELECT rol FROM afiliados WHERE curp = '$curp_usuario' LIMIT 1";
$resRol = $conn->query($sqlRol);

if (!$resRol || $resRol->num_rows == 0) {
    die("<p>Usuario no encontrado</p>");
}
$rolUsuario = $resRol->fetch_assoc()['rol'];

// --------------------------------------------------------------------
// CASO 1: SI EL USUARIO ES COORDINADOR
// --------------------------------------------------------------------
if ($rolUsuario == "coordinador") {
    // Buscar todos los líderes de este coordinador
    $sqlLideres = "SELECT id, curp, nombre, apellidos, foto 
                   FROM afiliados 
                   WHERE curp_id_coordinador = '$curp_usuario' AND rol = 'lider'";
    $resLideres = $conn->query($sqlLideres);

    if ($resLideres && $resLideres->num_rows > 0) {
        $totalGeneral = 0;
        $totalLideres = $resLideres->num_rows;

    
        echo "<h2 style='margin:5px 0; font-size:16px;'>
               Líderes Registrado por ti: <b>( $totalLideres )</b> &nbsp;&nbsp; 
              </h2>";

        echo "<ul style='list-style: none; padding: 0; margin:0;'>";

        while ($lider = $resLideres->fetch_assoc()) {
            $idLider = $lider['id'];
            $curpLider = $lider['curp'];
            $nombreLider = $lider['nombre'] . " " . $lider['apellidos'];
            $fotoLider = $lider['foto'];

            // Contar sublíderes de este líder
            $sqlSub = "SELECT COUNT(*) as total FROM afiliados 
                       WHERE curp_id_lider = '$curpLider' AND rol = 'sublider'";
            $resSub = $conn->query($sqlSub);
            $totalSub = ($resSub && $resSub->num_rows > 0) ? $resSub->fetch_assoc()['total'] : 0;

            // Contar afiliados de este líder (directos + registrados por sus sublíderes)
            $sqlAfiliados = "SELECT COUNT(*) as total FROM afiliados 
                             WHERE curp_id_lider = '$curpLider' OR curp_id_sublider IN (
                                 SELECT curp FROM afiliados WHERE curp_id_lider = '$curpLider' AND rol = 'sublider'
                             )";
            $resAfiliados = $conn->query($sqlAfiliados);
            $totalAfiliados = ($resAfiliados && $resAfiliados->num_rows > 0) ? $resAfiliados->fetch_assoc()['total'] : 0;

            $totalGeneral += $totalAfiliados;

            // Mostrar líder como tarjeta clickeable
            echo "
            <a href='vista.html?id={$idLider}' style='text-decoration:none; color:inherit;'>
                <li class='card-item' style='background:#f1f8e9;'>
            ";
            // Foto del líder
            if (!empty($fotoLider)) {
                echo "<img src='../php/uploads/$fotoLider' alt='Foto'
                       style='width:60px; height:60px; border-radius:50%; object-fit:cover; flex-shrink:0;'>";
            } else {
                echo "<div style='width:60px; height:60px; border-radius:50%; background:#ccc; flex-shrink:0;'></div>";
            }

            echo "
                    <div style='margin-left:12px; flex:1; text-align:left;'>
                        <h1 class='textlista'>$nombreLider</h1>
                        <h2 style='margin:2px 0; font-size:14px; color:#555;'>($totalSub) SubLíderes</h2>
                        <h2 style='margin:2px 0; font-size:14px; color:#555;'>($totalAfiliados) Afiliados</h2>
                    </div>
                </li>
            </a>
            ";
        }

        echo "</ul>";
        echo "<hr style='margin:20px 0; border:1px solid #ddd;'>";
        echo "<h2 style='text-align:right; color:#333; font-size:20px;'>Total general: $totalGeneral</h2>";
    } else {
        echo "<h1 style='font-size: 16px; margin: 0px;'>No hay líderes registrados bajo este coordinador</h1>";
    }
}
// --------------------------------------------------------------------
// CASO 2: SI EL USUARIO ES LÍDER O SUBLÍDER (igual que antes)
// --------------------------------------------------------------------
else {
    $sql = "SELECT id, nombre, apellidos, foto, curp_id_lider, curp_id_sublider 
            FROM afiliados  
            WHERE curp_id_lider = '$curp_usuario' 
               OR curp_id_sublider = '$curp_usuario'
            ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $registrados_por_usuario = [];
        $registrados_por_sublider = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['curp_id_lider'] == $curp_usuario && (empty($row['curp_id_sublider']) || $row['curp_id_sublider'] == $curp_usuario)) {
                $registrados_por_usuario[] = $row;
            } elseif ($row['curp_id_sublider'] == $curp_usuario) {
                $registrados_por_usuario[] = $row;
            } else {
                if (!empty($row['curp_id_sublider'])) {
                    $registrados_por_sublider[$row['curp_id_sublider']][] = $row;
                }
            }
        }

        echo "<ul style='list-style: none; padding: 0; margin:0;'>";

        if (!empty($registrados_por_usuario)) {
            $totalUsuario = count($registrados_por_usuario);
            echo "<h3 style='margin:10px 0; color:#333;'>Registrados por ti ($totalUsuario)</h3>";

            foreach ($registrados_por_usuario as $row) {
                echo "
                <a href='vista.html?id={$row['id']}' style='text-decoration:none; color:inherit;'>
                    <li class='card-item' style='background:#ffffff;'>
                ";
                if (!empty($row['foto'])) {
                    echo "<img src='../php/uploads/{$row['foto']}' alt='Foto'
                           style='width:60px; height:60px; border-radius:50%; object-fit:cover; flex-shrink:0;'>";
                } else {
                    echo "<div style='width:60px; height:60px; border-radius:50%; background:#ccc; flex-shrink:0;'></div>";
                }
                echo "
                        <div style='margin-left:12px; text-align:left;'>
                            <h1 class='textlista'>{$row['nombre']} {$row['apellidos']}</h1>
                        </div>
                    </li>
                </a>
                ";
            }
        }

        if (!empty($registrados_por_usuario) && !empty($registrados_por_sublider)) {
            echo "<hr style='margin:20px 0; border:1px solid #ddd;'>";
        }

        if (!empty($registrados_por_sublider)) {
            foreach ($registrados_por_sublider as $curp_id_sublider => $listaAfiliados) {
                $sqlSublider = "SELECT nombre, apellidos FROM afiliados WHERE curp = '$curp_id_sublider' LIMIT 1";
                $resSublider = $conn->query($sqlSublider);

                $nombreSublider = "Sublíder";
                if ($resSublider && $resSublider->num_rows > 0) {
                    $sub = $resSublider->fetch_assoc();
                    $nombreSublider = $sub['nombre'] . " " . $sub['apellidos'];
                }

                $totalAfiliados = count($listaAfiliados);
                echo "<h3 class='promotor-titulo'>Registrados por promotor: $nombreSublider ($totalAfiliados)</h3>";

                foreach ($listaAfiliados as $row) {
                    echo "
                    <a href='vista.html?id={$row['id']}' style='text-decoration:none; color:inherit;'>
                        <li class='card-item' style='background:#fff9c4;'>
                    ";
                    if (!empty($row['foto'])) {
                        echo "<img src='../php/uploads/{$row['foto']}' alt='Foto'
                               style='width:60px; height:60px; border-radius:50%; object-fit:cover; flex-shrink:0;'>";
                    } else {
                        echo "<div style='width:60px; height:60px; border-radius:50%; background:#ccc; flex-shrink:0;'></div>";
                    }
                    echo "
                            <div style='margin-left:12px; text-align:left;'>
                                <h1 class='textlista'>{$row['nombre']} {$row['apellidos']}</h1>
                            </div>
                        </li>
                    </a>
                    ";
                }

                echo "<hr style='margin:20px 0; border:1px solid #ddd;'>";
            }
        }

        echo "</ul>";
    } else {
        echo "<h1 style='font-size: 16px; margin: 0px;'>No hay registros disponibles</h1>";
    }
}

$conn->close();
?>

<style>
    .card-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .textlista {
        font-size: 18px;
        margin: 0;
        color: #333;
    }
    .promotor-titulo {
        margin: 4px 0;
        font-size: 18px;
        font-weight: bold;
        color: #333;
        text-align: left;
    }
</style>
