<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$curpUsuario = $_SESSION['usuario_curp'] ?? '';

if ($curpUsuario === '' && isset($_COOKIE['login_usuario'])) {
    $curpUsuario = strtoupper(trim($_COOKIE['login_usuario']));
}

if ($curpUsuario === '' && isset($_GET['curp'])) {
    $curpUsuario = strtoupper(trim($_GET['curp']));
}

if ($curpUsuario === '') {
    echo '<p>No se recibió CURP del usuario</p>';
    $conn->close();
    exit;
}

$stmtRol = $conn->prepare('SELECT rol FROM afiliados WHERE curp = ? LIMIT 1');
if (!$stmtRol) {
    echo '<p>Error al consultar el rol del usuario</p>';
    $conn->close();
    exit;
}

$stmtRol->bind_param('s', $curpUsuario);
$stmtRol->execute();
$resRol = $stmtRol->get_result();

if (!$resRol || $resRol->num_rows === 0) {
    $stmtRol->close();
    echo '<p>Usuario no encontrado</p>';
    $conn->close();
    exit;
}

$rolUsuario = strtolower($resRol->fetch_assoc()['rol'] ?? '');
$stmtRol->close();

function renderFoto(?string $foto): string
{
    if ($foto && $foto !== '') {
        $src = '../php/uploads/' . rawurlencode($foto);
        return "<img src='{$src}' alt='Foto' style='width:60px; height:60px; border-radius:50%; object-fit:cover; flex-shrink:0;'>";
    }
    return "<div style='width:60px; height:60px; border-radius:50%; background:#ccc; flex-shrink:0;'></div>";
}

if ($rolUsuario === 'coordinador') {
    $stmtLideres = $conn->prepare("SELECT id, curp, nombre, apellidos, foto FROM afiliados WHERE curp_id_coordinador = ? AND rol = 'lider'");
    $stmtLideres->bind_param('s', $curpUsuario);
    $stmtLideres->execute();
    $resLideres = $stmtLideres->get_result();

    if ($resLideres && $resLideres->num_rows > 0) {
        $totalGeneral = 0;
        $totalLideres = $resLideres->num_rows;

        echo "<h2 style='margin:5px 0; font-size:16px;'>Líderes registrados por ti: <b>( {$totalLideres} )</b></h2>";
        echo "<ul style='list-style: none; padding: 0; margin:0;'>";

        while ($lider = $resLideres->fetch_assoc()) {
            $curpLider = $lider['curp'];

            $stmtSub = $conn->prepare("SELECT COUNT(*) AS total FROM afiliados WHERE curp_id_lider = ? AND rol = 'sublider'");
            $stmtSub->bind_param('s', $curpLider);
            $stmtSub->execute();
            $totalSub = ($stmtSub->get_result()->fetch_assoc()['total'] ?? 0);
            $stmtSub->close();

            $stmtAfi = $conn->prepare("SELECT COUNT(*) AS total FROM afiliados WHERE curp_id_lider = ? OR curp_id_sublider IN (SELECT curp FROM afiliados WHERE curp_id_lider = ? AND rol = 'sublider')");
            $stmtAfi->bind_param('ss', $curpLider, $curpLider);
            $stmtAfi->execute();
            $totalAfiliados = ($stmtAfi->get_result()->fetch_assoc()['total'] ?? 0);
            $stmtAfi->close();

            $totalGeneral += (int) $totalAfiliados;

            $nombreLider = htmlspecialchars(($lider['nombre'] ?? '') . ' ' . ($lider['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
            $foto = renderFoto($lider['foto'] ?? null);
            $idLider = (int) $lider['id'];

            echo "<a href='vista.html?id={$idLider}' style='text-decoration:none; color:inherit;'>";
            echo "<li class='card-item' style='background:#f1f8e9;'>";
            echo $foto;
            echo "<div style='margin-left:12px; flex:1; text-align:left;'>";
            echo "<h1 class='textlista'>{$nombreLider}</h1>";
            echo "<h2 style='margin:2px 0; font-size:14px; color:#555;'>({$totalSub}) SubLíderes</h2>";
            echo "<h2 style='margin:2px 0; font-size:14px; color:#555;'>({$totalAfiliados}) Afiliados</h2>";
            echo "</div></li></a>";
        }

        echo '</ul>';
        echo "<hr style='margin:20px 0; border:1px solid #ddd;'>";
        echo "<h2 style='text-align:right; color:#333; font-size:20px;'>Total general: {$totalGeneral}</h2>";
    } else {
        echo "<h1 style='font-size: 16px; margin: 0px;'>No hay líderes registrados bajo este coordinador</h1>";
    }

    $stmtLideres->close();
} else {
    $stmtLista = $conn->prepare('SELECT id, nombre, apellidos, foto, curp_id_lider, curp_id_sublider FROM afiliados WHERE curp_id_lider = ? OR curp_id_sublider = ? ORDER BY id DESC');
    $stmtLista->bind_param('ss', $curpUsuario, $curpUsuario);
    $stmtLista->execute();
    $resLista = $stmtLista->get_result();

    if ($resLista && $resLista->num_rows > 0) {
        $registradosPorUsuario = [];
        $registradosPorSublider = [];

        while ($row = $resLista->fetch_assoc()) {
            $curpIdLider = $row['curp_id_lider'] ?? '';
            $curpIdSublider = $row['curp_id_sublider'] ?? '';

            if ($curpIdLider === $curpUsuario && ($curpIdSublider === '' || $curpIdSublider === $curpUsuario)) {
                $registradosPorUsuario[] = $row;
            } elseif ($curpIdSublider === $curpUsuario) {
                $registradosPorUsuario[] = $row;
            } elseif ($curpIdSublider !== '') {
                $registradosPorSublider[$curpIdSublider][] = $row;
            }
        }

        echo "<ul style='list-style: none; padding: 0; margin:0;'>";

        if (!empty($registradosPorUsuario)) {
            $totalUsuario = count($registradosPorUsuario);
            echo "<h3 style='margin:10px 0; color:#333;'>Registrados por ti ({$totalUsuario})</h3>";

            foreach ($registradosPorUsuario as $row) {
                $nombre = htmlspecialchars(($row['nombre'] ?? '') . ' ' . ($row['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
                $foto = renderFoto($row['foto'] ?? null);
                $id = (int) $row['id'];

                echo "<a href='vista.html?id={$id}' style='text-decoration:none; color:inherit;'>";
                echo "<li class='card-item' style='background:#ffffff;'>";
                echo $foto;
                echo "<div style='margin-left:12px; text-align:left;'><h1 class='textlista'>{$nombre}</h1></div>";
                echo "</li></a>";
            }
        }

        if (!empty($registradosPorUsuario) && !empty($registradosPorSublider)) {
            echo "<hr style='margin:20px 0; border:1px solid #ddd;'>";
        }

        if (!empty($registradosPorSublider)) {
            $stmtNombre = $conn->prepare('SELECT nombre, apellidos FROM afiliados WHERE curp = ? LIMIT 1');

            foreach ($registradosPorSublider as $curpSublider => $listaAfiliados) {
                $stmtNombre->bind_param('s', $curpSublider);
                $stmtNombre->execute();
                $resNombre = $stmtNombre->get_result();
                $nombreSublider = 'Sublíder';
                if ($resNombre && $resNombre->num_rows > 0) {
                    $dato = $resNombre->fetch_assoc();
                    $nombreSublider = htmlspecialchars(($dato['nombre'] ?? '') . ' ' . ($dato['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
                }

                $totalAfiliados = count($listaAfiliados);
                echo "<h3 class='promotor-titulo'>Registrados por promotor: {$nombreSublider} ({$totalAfiliados})</h3>";

                foreach ($listaAfiliados as $row) {
                    $nombre = htmlspecialchars(($row['nombre'] ?? '') . ' ' . ($row['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $foto = renderFoto($row['foto'] ?? null);
                    $id = (int) $row['id'];

                    echo "<a href='vista.html?id={$id}' style='text-decoration:none; color:inherit;'>";
                    echo "<li class='card-item' style='background:#fff9c4;'>";
                    echo $foto;
                    echo "<div style='margin-left:12px; text-align:left;'><h1 class='textlista'>{$nombre}</h1></div>";
                    echo "</li></a>";
                }

                echo "<hr style='margin:20px 0; border:1px solid #ddd;'>";
            }

            $stmtNombre->close();
        }

        echo '</ul>';
    } else {
        echo "<h1 style='font-size: 16px; margin: 0px;'>No hay registros disponibles</h1>";
    }

    $stmtLista->close();
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
