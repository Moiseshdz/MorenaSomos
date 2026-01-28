<?php
session_start();
include 'config/conexion.php';

/* =======================================================
   🔐 1. VALIDACIÓN DE SESIÓN / COOKIE
   - Si no hay sesión, intenta restaurar desde cookie.
   - Si no hay ninguna, redirige al login.
======================================================= */
if (!isset($_SESSION['curp'])) {
    if (!isset($_COOKIE['login_usuario'])) {
        header("Location: index.php");
        exit;
    }
}

/* Función para normalizar rol a minúsculas */
function rol($r)
{
    return strtolower(trim((string) $r));
}

/* Si no hay sesión, restaurar datos desde cookie */
if (!isset($_SESSION['curp']) && isset($_COOKIE['login_usuario'])) {
    $curp = $_COOKIE['login_usuario'];
    foreach (['coordinador', 'lider', 'sublider', 'militante'] as $t) {
        $sql = "SELECT * FROM $t WHERE curp=? LIMIT 1";
        $st = $conn->prepare($sql);
        $st->bind_param("s", $curp);
        $st->execute();
        if ($row = $st->get_result()->fetch_assoc()) {
            if ($row['estatus'] === 'activo') {
                $_SESSION['curp'] = $row['curp'];
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['apellidos'] = $row['apellidos'];
                $_SESSION['rol'] = $row['rol'];
                $_SESSION['foto'] = $row['foto'] ?? '';
            }
            break;
        }
    }
}

/* Variables básicas */
$curp = $_SESSION['curp'] ?? '';
$rol = $_SESSION['rol'] ?? '';
$relacion = "Sin relación asignada";

// Definir relación jerárquica según el rol
if ($rol === 'coordinador') {
    $relacion = "Coordinador(a) General";
} elseif ($rol === 'lider') {
    $relacion = "Depende del Coordinador";
} elseif ($rol === 'sublider') {
    $relacion = "Depende del Líder";
} elseif ($rol === 'militante') {
    $relacion = "Depende del Sublíder";
}



/* =======================================================
   👥 2. RELACIONES SEGÚN ROL 
   - Si es sublíder, buscar líder y coordinador.
   - Si es militante, buscar sublíder, líder y coordinador.
   - El coordinador no tiene superior.
======================================================= */
if ($rol === 'lider') {
    // LÍDER → COORDINADOR
    $st = $conn->prepare("SELECT coordinador FROM lider WHERE curp=? LIMIT 1");
    $st->bind_param("s", $curp);
    $st->execute();
    if ($r = $st->get_result()->fetch_assoc()) {
        if (!empty($r['coordinador'])) {
            $st2 = $conn->prepare("SELECT nombre, apellidos FROM coordinador WHERE curp=? LIMIT 1");
            $st2->bind_param("s", $r['coordinador']);
            $st2->execute();
            if ($c = $st2->get_result()->fetch_assoc()) {
                $relacion = "Coordinador - {$c['nombre']} {$c['apellidos']}";
            }
        }
    }
} elseif ($rol === 'sublider') {
    // SUBLÍDER → LÍDER + COORDINADOR
    $st = $conn->prepare("SELECT lider FROM sublider WHERE curp=? LIMIT 1");
    $st->bind_param("s", $curp);
    $st->execute();
    if ($s = $st->get_result()->fetch_assoc()) {
        $partes = [];
        if (!empty($s['lider'])) {
            $st2 = $conn->prepare("SELECT nombre, apellidos, coordinador FROM lider WHERE curp=? LIMIT 1");
            $st2->bind_param("s", $s['lider']);
            $st2->execute();
            if ($l = $st2->get_result()->fetch_assoc()) {
                $partes[] = "Líder - {$l['nombre']} {$l['apellidos']}";
                if (!empty($l['coordinador'])) {
                    $st3 = $conn->prepare("SELECT nombre, apellidos FROM coordinador WHERE curp=? LIMIT 1");
                    $st3->bind_param("s", $l['coordinador']);
                    $st3->execute();
                    if ($c = $st3->get_result()->fetch_assoc()) {
                        $partes[] = "Coordinador - {$c['nombre']} {$c['apellidos']}";
                    }
                }
            }
        }
        if ($partes)
            $relacion = implode(" | ", $partes);
    }
} elseif ($rol === 'militante') {
    // MILITANTE → SUBLÍDER + LÍDER + COORDINADOR
    $st = $conn->prepare("SELECT sublider, lider, coordinador FROM militante WHERE curp=? LIMIT 1");
    $st->bind_param("s", $curp);
    $st->execute();
    if ($m = $st->get_result()->fetch_assoc()) {
        $partes = [];
        if (!empty($m['sublider'])) {
            $st1 = $conn->prepare("SELECT nombre, apellidos FROM sublider WHERE curp=? LIMIT 1");
            $st1->bind_param("s", $m['sublider']);
            $st1->execute();
            if ($s = $st1->get_result()->fetch_assoc()) {
                $partes[] = "Sublíder - {$s['nombre']} {$s['apellidos']}";
            }
        }
        if (!empty($m['lider'])) {
            $st2 = $conn->prepare("SELECT nombre, apellidos, coordinador FROM lider WHERE curp=? LIMIT 1");
            $st2->bind_param("s", $m['lider']);
            $st2->execute();
            if ($l = $st2->get_result()->fetch_assoc()) {
                $partes[] = "Líder - {$l['nombre']} {$l['apellidos']}";
                if (!empty($l['coordinador'])) {
                    $st3 = $conn->prepare("SELECT nombre, apellidos FROM coordinador WHERE curp=? LIMIT 1");
                    $st3->bind_param("s", $l['coordinador']);
                    $st3->execute();
                    if ($c = $st3->get_result()->fetch_assoc()) {
                        $partes[] = "Coordinador - {$c['nombre']} {$c['apellidos']}";
                    }
                }
            }
        }
        if (!empty($m['coordinador'])) {
            $st4 = $conn->prepare("SELECT nombre, apellidos FROM coordinador WHERE curp=? LIMIT 1");
            $st4->bind_param("s", $m['coordinador']);
            $st4->execute();
            if ($c = $st4->get_result()->fetch_assoc()) {
                #$partes[] = "Coordinador - {$c['nombre']} {$c['apellidos']}";
            }
        }
        if ($partes)
            $relacion = implode(" | ", $partes);
    }
}
// Coordinador → no tiene superior
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - App Morena</title>
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet" />
    <link rel="stylesheet" href="style/dashboard.css?v=23" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>

    <div class="dashboard-container" id="dashboardContainer">

        <!-- ======================================================
         🖼️ 3. TARJETA DE BIENVENIDA
         - Foto, nombre, apellidos, relación y CURP.
    ====================================================== -->
        <?php
        $curpLogeado = $_SESSION['curp'] ?? '';
        $foto = '';

        if ($curpLogeado) {
            foreach (['coordinador', 'lider', 'sublider', 'militante'] as $tabla) {
                $stmt = $conn->prepare("SELECT foto FROM $tabla WHERE curp=? LIMIT 1");
                $stmt->bind_param("s", $curpLogeado);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $foto = $row['foto'] ?? '';
                    break;
                }
            }
        }

        // Validar archivo físico en /uploads/
        if ($foto && file_exists(__DIR__ . "/uploads/" . $foto)) {
            $rutaFoto = "uploads/" . rawurlencode($foto);
        } else {
            $rutaFoto = "img/default-user.png";
        }
        ?>

        <div class="card-welcome">
            <img id="userFoto" src="<?= $rutaFoto ?>" alt="Foto Usuario" />
            <h3 style=" text-transform: uppercase; margin: 1px; margin-top: 16px;">
                <?= $_SESSION['nombre'] . " " . $_SESSION['apellidos'] ?>
            </h3>
            <p id="subtitle" style="margin: 1px;"><i class="fa fa-link"></i> <?= $relacion ?></p>
            <p style='font-family: Patria, "noso Sans", Helvetica, Arial, sans-serif; text-transform: uppercase;'>CURP:
                <?= $_SESSION['curp'] ?>
            </p>
        </div>

        <!-- ======================================================
         📋 4. MENÚ DE TARJETAS
         - Acceso rápido a secciones: Registrar, Lista, Reportes.
    ====================================================== -->
        <div class="menu-cards">
            <div class="menu-card" onclick="mostrarSeccion('registro')">
                <i class="fa fa-user-plus"></i>
                <h1 style="font-size: 16px; margin: 0px;">Registrar</h1>
            </div>
            <div class="menu-card" onclick="mostrarSeccion('tabla')">
                <i class="fa fa-chart-bar"></i>
                <h1 style="font-size: 16px; margin: 0px;">Lista</h1>
            </div>
            <div class="menu-card" onclick="mostrarSeccion('reportes')">
                <i class="fa fa-table"></i>
                <h1 style="font-size: 16px; margin: 0px;">Reportes</h1>
            </div>
        </div>

        <!-- ======================================================
         📂 5. SECCIONES
         - Registro, Lista, Reportes, Home, Notificaciones, Ajustes.
    ====================================================== -->

        <!-- Registro -->
        <div class="seccion" id="seccion-registro" style="margin-top:20px; text-align:center; margin-bottom:60px;">
            <style>
                .step {
                    display: none;
                }

                .step.active {
                    display: block;
                }

                .progress {
                    height: 25px;
                }

                .progress-bar {
                    font-weight: bold;
                }
            </style>
            </head>

            <body class="bg-light">
                <center>
                    <!-- Contenedor de notificaciones -->
                    <div id="notificationContainer"
                        style="position: fixed; top: 20px; left: 50%; 
                      transform: translateX(-50%); z-index: 9999; max-width: 400px; width: 90%; text-align: center; font-family: Patria, 'noso Sans', Helvetica, Arial, sans-serif;">
                    </div>


                </center>
                <div class="container mt-5">
                    <h2 class="text-center mb-4">Registro</h2>

                    <!-- Barra de progreso -->
                    <div class="progress mb-4">
                        <div id="progressBar" class="progress-bar" role="progressbar"
                            style="width: 25%; background:#FDC745;"></div>
                    </div>

                    <form id="registroForm" action="guardar.php" method="POST" enctype="multipart/form-data">


                        <!-- Paso 1: Datos personales -->
                        <div class="step active">
                            <div class="mb-3">
                                <label for="curp" class="form-label">CURP</label>
                                <input type="text" class="form-control" id="curp" name="curp" required maxlength="18"
                                    pattern="^[A-Z]{4}\d{6}[HM]{1}[A-Z]{5}[0-9A-Z]{2}$"
                                    title="Ingrese una CURP válida, por ejemplo: GOML950427HMCLNS09">

                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fecha de nacimiento</label>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control" name="dia" placeholder="Día"
                                        pattern="\d{1,2}" maxlength="2" required>
                                    <input type="text" class="form-control" name="mes" placeholder="Mes"
                                        pattern="\d{1,2}" maxlength="2" required>
                                    <input type="text" class="form-control" name="anios" placeholder="Año"
                                        pattern="\d{4}" maxlength="4" required>

                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Contacto -->
                        <div class="step">
                            <div class="mb-3">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select class="form-control" id="sexo" name="sexo"
                                    style="font-family: Patria, 'noso Sans', Helvetica, Arial, sans-serif;">
                                    <option value="">Seleccione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="O">Otro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estado" class="form-label">Seccion</label>
                                <input type="text" class="form-control" id="estado" name="seccion">
                            </div>
                            <div class="mb-3">
                                <label for="domicilio" class="form-label">Domicilio</label>
                                <input type="text" class="form-control" id="domicilio" name="domicilio">
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>





                        <!-- Paso 3: Jerárquico -->
                        <div class="step">


                            <?php
                            // CURP del logueado
                            $curpLogeado = $_SESSION['curp'] ?? '';

                            // Primero buscamos qué rol tiene el logueado y con quién está relacionado
                            $sql = "
    SELECT 'coordinador' AS rol, c.curp, NULL AS coordinador, NULL AS lider, NULL AS sublider
    FROM coordinador c WHERE c.curp = ?
    UNION
    SELECT 'lider' AS rol, l.curp, l.coordinador AS coordinador, NULL AS lider, NULL AS sublider
    FROM lider l WHERE l.curp = ?
    UNION
    SELECT 'sublider' AS rol, s.curp, s.coordinador AS coordinador, s.lider AS lider, NULL AS sublider
    FROM sublider s WHERE s.curp = ?
    UNION
    SELECT 'militante' AS rol, m.curp, m.coordinador AS coordinador, m.lider AS lider, m.sublider AS sublider
    FROM militante m WHERE m.curp = ?
    LIMIT 1
";

                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ssss", $curpLogeado, $curpLogeado, $curpLogeado, $curpLogeado);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($row = $result->fetch_assoc()) {
                                $rolLogeado = $row['rol'];

                                // Asignar el rol siguiente según la jerarquía
                                switch ($rolLogeado) {
                                    case 'coordinador':
                                        $rolNuevo = 'lider';
                                        $curpCoordinador = $curpLogeado; // siempre hay uno
                                        $curpLider = null;
                                        $curpSublider = null;
                                        break;

                                    case 'lider':
                                        $rolNuevo = 'sublider';
                                        $curpCoordinador = $row['coordinador'];
                                        $curpLider = $curpLogeado;
                                        $curpSublider = null;
                                        break;

                                    case 'sublider':
                                        $rolNuevo = 'militante';
                                        $curpCoordinador = $row['coordinador'];
                                        $curpLider = $row['lider'];
                                        $curpSublider = $curpLogeado;
                                        break;

                                    default:
                                        $rolNuevo = 'militante';
                                        $curpCoordinador = $row['coordinador'] ?? null;
                                        $curpLider = $row['lider'] ?? null;
                                        $curpSublider = $row['sublider'] ?? null;
                                        break;
                                }
                            }
                            ?>

                            <!-- Formulario con valores ya asignados -->
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <input type="text" class="form-control" id="rol" name="rol"
                                    value="<?= htmlspecialchars($rolNuevo) ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="coordinador" class="form-label">CURP Coordinador</label>
                                <input type="text" class="form-control" id="coordinador" name="coordinador"
                                    value="<?= htmlspecialchars($curpCoordinador !== null && $curpCoordinador !== '' ? $curpCoordinador : 'Null') ?>"
                                    readonly>
                            </div>

                            <div class="mb-3">
                                <label for="lider" class="form-label">CURP Líder</label>
                                <input type="text" class="form-control" id="lider" name="lider"
                                    value="<?= htmlspecialchars($curpLider !== null && $curpLider !== '' ? $curpLider : 'Null') ?>"
                                    readonly>
                            </div>

                            <div class="mb-3">
                                <label for="sublider" class="form-label">CURP SubLíder</label>
                                <input type="text" class="form-control" id="sublider" name="sublider"
                                    value="<?= htmlspecialchars($curpSublider !== null && $curpSublider !== '' ? $curpSublider : 'Null') ?>"
                                    readonly>
                            </div>

                        </div>

                        <!-- Paso 4: Foto y Confirmación -->
                        <div class="step">
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto de Registrado</label>
                                <input type="file" class="form-control" id="foto" name="foto" importance="low"
                                    accept="image/*">
                            </div>


                            <div class="mb-3">
                                <label for="estatus" class="form-label">Estatus</label>
                                <input type="text" class="form-control" id="estatus" name="estatus" value="activo">
                            </div>
                            <p class="text-muted">Revisa que todos los datos sean correctos antes de enviar.</p>
                        </div>




                        <!-- Botones de navegación -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-secondary w-50 me-3"
                                onclick="nextPrev(-1)">Anterior</button>

                            <button type="button" id="nextBtn" class="btn btn-primary w-50 ms-3"
                                onclick="nextPrev(1)">Siguiente</button>
                        </div>


                    </form>
                </div>
        </div>

        <!-- Lista -->
        <div class="seccion" id="seccion-tabla" style="margin-top:20px; text-align:center; margin-bottom:60px;">
            <h2>Lista de Registros</h2>
            <?php
            $curpLogeado = $_SESSION['curp'] ?? '';
            $rol = $_SESSION['rol'] ?? '';

            if ($curpLogeado && $rol) {

                // Definimos la consulta según el rol
                switch ($rol) {
                    case 'coordinador':
                        $registtre = "SELECT id, nombre, apellidos, curp, foto FROM lider WHERE coordinador = '$curpLogeado'";
                        $tipoRegistro = "Líderes";
                        break;
                    case 'lider':
                        $registtre = "SELECT id, nombre, apellidos, curp, foto FROM sublider WHERE lider = '$curpLogeado'";
                        $tipoRegistro = "Sublíderes";
                        break;
                    case 'sublider':
                        $registtre = "SELECT id, nombre, apellidos, curp, foto FROM militante WHERE sublider = '$curpLogeado'";
                        $tipoRegistro = "Militantes";
                        break;
                    default:
                        $registtre = "";
                        $tipoRegistro = "";
                }

                if ($registtre) {
                    $query = $conn->query($registtre);

                    if ($query && $query->num_rows > 0) {
                        echo "<h3 style='margin:10px 0; color:#333; text-align:center;'>Por ti ({$query->num_rows}) - $tipoRegistro</h3>";
                        echo "<ul style='list-style: none; padding: 0; margin:0;'>";

                        while ($row = $query->fetch_assoc()) {
                            $foto = !empty($row['foto']) && file_exists(__DIR__ . "/uploads/" . $row['foto'])
                                ? "uploads/" . rawurlencode($row['foto'])
                                : "img/default-user.png";

                            // Contar registros debajo de esta persona
                            $totalRegistros = 0;
                            switch ($rol) {
                                case 'coordinador':
                                    // Contar sublíderes de este líder
                                    $countQuery = "SELECT COUNT(*) AS total FROM sublider WHERE lider='{$row['curp']}'";
                                    $resultCount = $conn->query($countQuery);
                                    $totalRegistros = ($resultCount) ? $resultCount->fetch_assoc()['total'] : 0;
                                    break;
                                case 'lider':
                                    // Contar militantes de este sublíder
                                    $countQuery = "SELECT COUNT(*) AS total FROM militante WHERE sublider='{$row['curp']}'";
                                    $resultCount = $conn->query($countQuery);
                                    $totalRegistros = ($resultCount) ? $resultCount->fetch_assoc()['total'] : 0;
                                    break;
                                case 'sublider':
                                    $totalRegistros = 0; // Los militantes no registran a nadie
                                    break;
                            }
                            ?>

                            <a href="vista.php?id=<?= $row['id'] ?>" style="text-decoration:none; color:inherit;">
                                <li class="card-item"
                                    style="background:#ffffff; display:flex; align-items:center; padding:8px; margin-bottom:6px; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                    <img src="<?= $foto ?>" alt="Foto"
                                        style="width:60px; height:60px; border-radius:50%; object-fit:cover; flex-shrink:0;">
                                    <div style="margin-left:12px; text-align:left;">
                                        <h1 class="textlista" style="font-size:16px; margin:0; text-transform: uppercase;">
                                            <?= $row['nombre'] . ' ' . $row['apellidos'] ?>
                                        </h1>
                                        <h2 style="margin:2px 0; font-size:14px; color:#555;">
                                            CURP: <?= $row['curp'] ?>
                                        </h2>
                                        <h2 style="margin:2px 0; font-size:14px; color:#555;">
                                            Registros: <?= $totalRegistros ?>
                                        </h2>
                                    </div>
                                </li>
                            </a>

                            <?php
                        }

                        echo "</ul>";
                    } else {
                        echo "<p style='text-align:center;'>No se encontraron registros.</p>";
                    }
                } else {
                    echo '<p style="text-align:center; font-family: Patria, \'Noto Sans\', Helvetica, Arial, sans-serif;">Rol no reconocido / Aún no hay registros</p>';
                }
            } else {
                echo "<p style='text-align:center;'>No hay sesión activa.</p>";
            }
            ?>
        </div>



        <!-- Reportes -->
        <div class="seccion" id="seccion-reportes" style="margin-top:20px; text-align:center; margin-bottom:60px;">
            <h2>Reporte Gráfico por Sección</h2>


            <?php
            include 'config/conexion.php';
          

            $curpLogeado = $_SESSION['curp'] ?? '';

            if (empty($curpLogeado)) {
                die("No hay CURP logeado. Inicia sesión primero.");
            }

            // Consulta combinada de todas las tablas relacionadas
            $sql = "
        SELECT seccion FROM lider WHERE coordinador = '$curpLogeado'
        UNION
        SELECT seccion FROM sublider WHERE lider = '$curpLogeado'
        UNION
        SELECT seccion FROM militante WHERE sublider = '$curpLogeado'
    ";

            $query = mysqli_query($conn, $sql);

            if (!$query) {
                die("Error en la consulta SQL: " . mysqli_error($conn));
            }

            $secciones = [];
            $cont = 1;

            while ($row = mysqli_fetch_assoc($query)) {
                $sec = $row['seccion'] ?: 'Sin sección';

                if (!array_key_exists($sec, $secciones)) {
                    $countSql = "
                SELECT COUNT(*) AS total FROM (
                    SELECT seccion FROM lider WHERE coordinador = '$curpLogeado' AND seccion = '$sec'
                    UNION ALL
                    SELECT seccion FROM sublider WHERE lider = '$curpLogeado' AND seccion = '$sec'
                    UNION ALL
                    SELECT seccion FROM militante WHERE sublider = '$curpLogeado' AND seccion = '$sec'
                ) AS totalRegistros
            ";
                    $countQuery = mysqli_query($conn, $countSql);
                    $countRow = mysqli_fetch_assoc($countQuery);
                    $totalPorSeccion = $countRow['total'] ?? 0;

                    $secciones[$sec] = $totalPorSeccion;
                }
            }

            // Convertir para gráficas
            $labels = json_encode(array_keys($secciones));
            $valores = json_encode(array_values($secciones));
            ?>

            <ol id="listaSecciones" style="text-align:left; display:inline-block; list-style:none; padding:0;  font-family: Patria, 'noso Sans', Helvetica, Arial, sans-serif; "></ol>

            <div id="graficas"
                style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 25px; justify-items: center; margin-top:40px;">
                <canvas id="graficoBarras" style="max-width: 400px;"></canvas>
                <canvas id="graficoLineas" style="max-width: 400px;"></canvas>
                <canvas id="graficoPastel" style="max-width: 400px;"></canvas>
            </div>

            <!-- Librería Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const labels = <?php echo $labels; ?>;
                const dataValues = <?php echo $valores; ?>;

                // Generar colores fijos por sección
                const colores = labels.map(() => `hsl(${Math.random() * 360}, 70%, 60%)`);

                // ==== Mostrar lista con colores ====
                const lista = document.getElementById("listaSecciones");
                labels.forEach((label, i) => {
                    const li = document.createElement("li");
                    li.innerHTML = `
                <span style="display:inline-block; width:15px; height:15px; background:${colores[i]}; border-radius:3px; margin-right:8px;"></span>
                <b>${label}</b> <i class="fa fa-chevron-right"></i> Total: <b>${dataValues[i]}</b>
            `;
                    lista.appendChild(li);
                });

                // ==== Gráfico de Barras ====
                new Chart(document.getElementById('graficoBarras').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Totales por Sección',
                            data: dataValues,
                            backgroundColor: colores,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });

                // ==== Gráfico de Líneas ====
                new Chart(document.getElementById('graficoLineas').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Evolución por Sección',
                            data: dataValues,
                            fill: false,
                            borderColor: '#007bff',
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'top' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });

                // ==== Gráfico de Pastel ====
                new Chart(document.getElementById('graficoPastel').getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: colores
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            </script>
        </div>






        <!-- Home -->
        <div class="seccion" id="seccion-home">
            <?php

            $anuncios = "SELECT * FROM anuncios ORDER BY fecha DESC LIMIT 5";
            $result = $conn->query($anuncios);
            if ($result && $result->num_rows > 0) {
                echo "<div id='anuncios' style='margin-top:20px; text-align:center; margin-bottom:60px;'>";
                while ($row = $result->fetch_assoc()) {
                    $tipo = htmlspecialchars($row['tipo']);
                    $titulo = htmlspecialchars($row['titulo']);
                    $mensaje = htmlspecialchars($row['descripcion']);

                    if ($tipo == "Anuncio Importante") {
                        $icon = "📢";
                        $semaforo = "#F8D7DA"; // Rojo claro
                    } elseif ($tipo == "Recordatorio") {
                        $icon = "📅";
                        $semaforo = "#FFF3CD"; // Amarillo claro
                    } elseif ($tipo == "Alerta") {
                        $icon = "⚠️";
                        $semaforo = "#FFF3CD"; // Amarillo claro
                    } else {
                        $icon = "ℹ️ Anuncio";
                        $semaforo = "#F4F4F4"; // Azul claro
                    }
                    ?>
                    <div id="anuncios" style="margin-top:20px; text-align:center; margin-bottom:60px;">
                        <div
                            style="background:<?= $semaforo ?>; border:1px solid #ccc; padding:15px; border-radius:8px; margin-bottom:10px;">
                            <h3 style="color:#611232; margin:0;"><?= $icon ?><?= $tipo ?></h3>
                            <hr>
                            <h3 style="margin:0; font-size:22px; color: #e9a811ff;">
                                <?= $titulo ?>
                            </h3>

                            <p style="margin:5px 0 0;font-family: Patria, 'noso Sans', Helvetica, Arial, sans-serif;">
                                <?= $mensaje ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
                echo "</div>";
            } else {
                echo "<div id='anuncios' style='margin-top:20px; text-align:center; margin-bottom:60px;'>
                    <p>No hay anuncios disponibles.</p>
                  </div>";
            }
            ?>
        </div>




        <!-- Notificaciones -->
        <div class="seccion" id="seccion-notificaciones">
            <h2 style="text-align:center; color:#611232;">🔔 Alertas</h2>
            <?php
            $curpLogeado = $_SESSION['curp'] ?? ''; // CURP del usuario logeado
            
            $lertas = "SELECT * FROM alertas WHERE curp_alerta = '$curpLogeado' ORDER BY fecha DESC";
            $result = $conn->query($lertas);

            if ($result && $result->num_rows > 0) {
                echo "<ul style='list-style: none; padding: 0; margin:0;'>";
                while ($row = $result->fetch_assoc()) {
                    $titulo = htmlspecialchars($row['titulo']);
                    $mensaje = htmlspecialchars($row['alertas']);
                    $fecha = date("d/m/Y H:i", strtotime($row['fecha']));

                    // Para dar color según el título
                    if (stripos($titulo, "Registro") !== false) {
                        $icon = "📝";
                        $semaforo = "#FFF3CD"; // Amarillo claro
                    } elseif (stripos($titulo, "Error") !== false) {
                        $icon = "⚠️";
                        $semaforo = "#F8D7DA"; // Rojo claro
                    } else {
                        $icon = "🔔";
                        $semaforo = "#D1E7DD"; // Verde claro
                    }

                    echo "
        <li class='card-item' style='background:$semaforo; margin-bottom:10px; padding:10px; border-radius:6px;'>
            <div style='margin-left:12px; text-align:left;'>
                <h1 class='textlista' style='font-size:16px; margin:0;'>{$icon} {$titulo}</h1>
                <h2 style='margin:2px 0; font-size:14px; color:#555;'>{$mensaje}</h2>
                <p style='font-size:12px; color:#777; margin:0;'>Fecha: {$fecha}</p>
            </div>
        </li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='text-align:center;'>No hay notificaciones.</p>";
            }
            ?>
        </div>



        <!-- Ajustes -->
        <div class="seccion" id="seccion-ajustes">
            <h2 style="text-align:center; color:#611232;">⚙️ Ajustes</h2>
            <div style="margin-top:20px;">
                <div class="ajuste-item">
                    <i class="fa fa-user-circle"></i>
                    <h1 id="ajustetext">Editar perfil</h1>
                    <button class="btn-ajuste" onclick="alert('Abrir edición de perfil')">➡</button>
                </div>
                <div class="ajuste-item">
                    <i class="fa fa-lock"></i>
                    <h1 id="ajustetext">Cambiar contraseña</h1>
                    <button class="btn-ajuste" onclick="alert('Abrir cambio de contraseña')">➡</button>
                </div>
                <div class="ajuste-item">
                    <i class="fa fa-bell"></i>
                    <h1 id="ajustetext">Notificaciones</h1>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="ajuste-item">
                    <i class="fa fa-moon"></i>
                    <h1 id="ajustetext">Modo oscuro</h1>
                    <label class="switch">
                        <input type="checkbox" onchange="toggleDarkMode(this)">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="ajuste-item logout" id="logoutItem">
                    <i class="fa fa-sign-out-alt"></i>
                    <h1 id="logoutBtn">Cerrar sesión</h1>
                    <button class="btn-ajuste btn-logout" id="logoutBtn">➡</button>
                </div>
            </div>
        </div>
        <br><br>
    </div>

    <!-- ======================================================
     📌 6. NAVEGACIÓN INFERIOR
======================================================= -->
    <div class="bottom-nav">
        <button class="active" onclick="mostrarSeccion('home', this)">
            <i class="fa fa-home"></i>
            <h1 style="font-size: 16px; margin: 0px;">Inicio</h1>
        </button>
        <button onclick="mostrarSeccion('ajustes', this)">
            <i class="fa fa-cog"></i>
            <h1 style="font-size: 16px; margin: 0px;">Ajustes</h1>
        </button>
        <button onclick="mostrarSeccion('notificaciones', this)">
            <i class="fa fa-bell"></i>
            <h1 style="font-size: 16px; margin: 0px;">Alertas</h1>
        </button>
    </div>

    <script src="js/dashboard.js?v=30"></script>
</body>

</html>