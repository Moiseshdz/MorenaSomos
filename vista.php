<?php
session_start();
include 'config/conexion.php';

/* ==============================
   1. VALIDAR ID RECIBIDO
============================== */
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("<p class='alert alert-danger text-center'>ID inválido</p>");
}

/* ==============================
   2. DETECTAR ROL DEL REGISTRO
============================== */
$afiliado = null;
$tablas = ['coordinador', 'lider', 'sublider', 'militante'];

foreach ($tablas as $tabla) {
    $sql = "SELECT *, '$tabla' AS rol_tabla FROM $tabla WHERE id=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $afiliado = $res->fetch_assoc();
        break;
    }
}

if (!$afiliado) {
    die("<p class='alert alert-danger text-center'>Registro no encontrado</p>");
}

/* ==============================
   3. DETERMINAR FOTO
============================== */
$foto = !empty($afiliado['foto']) && file_exists(__DIR__ . "/uploads/" . $afiliado['foto'])
    ? "uploads/" . rawurlencode($afiliado['foto'])
    : "img/default-user.png";

/* ==============================
   4. DATOS DE SECCIÓN DE VOTACIÓN
============================== */
$seccion = $afiliado['seccion'] ?? '';
$cp = $colonia = $referencia = 'Sin datos';

if (!empty($seccion)) {
    $sql = "SELECT * FROM secciones WHERE seccion = '$seccion'";
    $query = $conn->query($sql);
    if ($query && $query->num_rows > 0) {
        $datosSeccion = $query->fetch_assoc();
        $cp = $datosSeccion['cp'] ?? 'Sin CP';
        $colonia = $datosSeccion['colonia'] ?? 'Sin colonia';
        $referencia = $datosSeccion['referencia'] ?? 'Sin referencia';
    } else {
        $cp = 'No encontrado';
        $colonia = 'No encontrada';
        $referencia = 'No encontrada';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil - <?= htmlspecialchars($afiliado['nombre'] ?? '-') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">

    <style>
        /* ==============================
           ESTILOS GENERALES
        =============================== */
        body {
            font-family: Patria, "noso Sans", Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f2f3f7;
            color: #333;
            text-transform: uppercase;
        }

        .header {
            background: #611232;
            color: #fff;
            text-align: center;
            padding: 1rem;
            font-size: 18px;
            font-weight: bold;
        }

        /* ==============================
           TARJETA PERFIL
        =============================== */
        .profile-card {
            background: #fff;
            margin: 15px;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #611232;
        }

        .profile-card h2 {
            margin: 10px 0 5px;
            font-size: 20px;
            color: #611232;
        }

        .profile-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        /* ==============================
           SECCIÓN INFORMACIÓN
        =============================== */
        .section-card {
            background: #fff;
            margin: 0.5rem 1rem;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .section-card h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #444;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .field {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 15px;
            border-bottom: 1px solid #f1f1f1;
        }

        .field:last-child {
            border-bottom: none;
        }

        .field span:first-child {
            font-weight: bold;
            color: #555;
        }

        /* ==============================
           BOTÓN VOLVER
        =============================== */
        .btn-volver {
            display: block;
            width: 90%;
            max-width: 300px;
            margin: 25px auto;
            padding: 12px;
            background: #611232;
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .btn-volver:hover {
            background: #922222;
        }

        /* ==============================
           SWITCH TOGGLE
        =============================== */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #611232;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
</head>

<body>

    <!-- ==============================
       SWITCH PARA ACTIVAR EDICIÓN
    =============================== -->
    <div style="text-align:center; margin:10px 0;">
        Activar edición <br>
        <label class="switch">
            <input type="checkbox" id="editarSwitch">
            <span class="slider round"></span>
        </label>
    </div>

    <!-- ==============================
       TARJETA PRINCIPAL PERFIL
    =============================== -->
    <a href="dashboard.php" class="btn-volver">Volver</a>

    <div class="profile-card">
        <img src="<?= $foto ?>" alt="Foto">
        <h2><?= htmlspecialchars($afiliado['nombre'] . " " . $afiliado['apellidos']) ?></h2>
        <?php
        $estatus = $afiliado['estatus'] ?? '-';
        $color = ($estatus === 'activo') ? 'green' : 'red';
        ?>
        <p><strong>Estatus:</strong> <span
                style="color:<?= $color ?>; font-weight:bold;"><?= htmlspecialchars($estatus) ?></span></p>
        <p><strong>Rol:</strong> <?= htmlspecialchars($afiliado['rol_tabla']) ?></p>
    </div>

    <!-- ==============================
       SECCIÓN INFORMACIÓN PERSONAL
    =============================== -->
    <form action="">
        <div class="section-card">
            <h3>Información Personal</h3>
            <div class="field"><span>CURP</span><span class="editable"
                    data-type="text"><?= htmlspecialchars($afiliado['curp'] ?? '-') ?></span></div>
            <div class="field"><span>Nombre</span><span class="editable"
                    data-type="text"><?= htmlspecialchars($afiliado['nombre'] ?? '-') ?></span></div>
            <div class="field"><span>Apellidos</span><span class="editable"
                    data-type="text"><?= htmlspecialchars($afiliado['apellidos'] ?? '-') ?></span></div>
            <div class="field"><span>Fecha
                    Nac.</span><span><?= htmlspecialchars(($afiliado['dia'] ?? '') . "/" . ($afiliado['mes'] ?? '') . "/" . ($afiliado['anios'] ?? '')) ?></span>
            </div>
            <div class="field"><span>Sexo</span><span><?= htmlspecialchars($afiliado['sexo'] ?? '-') ?></span></div>

            <div class="field"><span>Domicilio</span></div>
            <div class="field">
                <div class="editable" data-type="textarea"
                    style="width:100%; font-size:14px; text-transform:uppercase; border:1px solid #ccc; padding:6px; border-radius:6px; background:#f9f9f9;">
                    <?= htmlspecialchars($afiliado['domicilio'] ?? '-') ?>
                </div>
            </div>

            <div class="field"><span>Sección de votación</span><span class="editable"
                    data-type="text"><?= htmlspecialchars($afiliado['seccion'] ?? '-') ?></span></div>
            <div class="field">
                <div class="editable" data-type="textarea"
                    style="width:100%; font-size:14px; text-transform:uppercase; border:1px solid #ccc; padding:6px; border-radius:6px; background:#f9f9f9;">
                    <?= strtoupper(htmlspecialchars($colonia . ', ' . $referencia)) ?>
                </div>
            </div>

            <div class="field"><span>Teléfono</span><span class="editable"
                    data-type="text"><?= htmlspecialchars($afiliado['telefono'] ?? '-') ?></span></div>
        </div>

        <!-- ==============================
       SECCIÓN JERARQUÍA
    =============================== -->
        <div class="section-card">
            <h3>Jerarquía</h3>
            <div class="field">
                <span>Rol</span><span><?= htmlspecialchars($afiliado['rol'] ?? $afiliado['rol_tabla']) ?></span>
            </div>
            <div class="field"><span>Estatus</span><span
                    style="color:<?= $color ?>; font-weight:bold;"><?= htmlspecialchars($estatus) ?></span></div>
            <div class="field">
                <span>Coordinador</span><span><?= htmlspecialchars($afiliado['coordinador'] ?? '-') ?></span>
            </div>
            <div class="field"><span>Líder</span><span><?= htmlspecialchars($afiliado['lider'] ?? '-') ?></span></div>
            <div class="field"><span>Registrado
                    el</span><span><?= htmlspecialchars($afiliado['registrado_el'] ?? '-') ?></span></div>
        </div>

        <!-- ==============================
       BOTÓN VOLVER
    =============================== -->
        <button id="btnGuardar" class="btn-volver" style="display:none;">GUARDAR</button>
    </form>




    <div class="section-card">
        <h3>Sus registros</h3>
        <?php
        $susRegistros = [];
        switch ($afiliado['rol_tabla']) {
            case 'coordinador':
                $sql = "SELECT * FROM lider WHERE coordinador = ?";
                $nivel = "Líderes";
                break;
            case 'lider':
                $sql = "SELECT * FROM sublider WHERE lider = ?";
                $nivel = "Sublíderes";
                break;
            case 'sublider':
                $sql = "SELECT * FROM militante WHERE sublider = ?";
                $nivel = "Militantes";
                break;
            default:
                $sql = "";
                $nivel = "";
        }

        if ($sql) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $afiliado['curp']);
            $stmt->execute();
            $res = $stmt->get_result();
            $susRegistros = $res->fetch_all(MYSQLI_ASSOC);
        }

        if (!empty($susRegistros)) {
            echo "<ul style='list-style:none; padding:0; margin:0;'>";
            foreach ($susRegistros as $r) {
                // Foto del registro
                $fotoRegistro = !empty($r['foto']) && file_exists(__DIR__ . "/uploads/" . $r['foto'])
                    ? "uploads/" . rawurlencode($r['foto'])
                    : "img/default-user.png";

                // Color según estatus
                $estatusRegistro = $r['estatus'] ?? '-';
                $color = ($estatusRegistro === 'activo') ? 'green' : 'red';

                echo "<li style='padding:8px; margin-bottom:6px; display:flex; align-items:center; border-radius:6px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.1);'>";
                echo "<img src='$fotoRegistro' style='width:50px; height:50px; border-radius:50%; object-fit:cover; flex-shrink:0; margin-right:12px;'>";
                echo "<div style='text-align:left;'>";
                echo "<strong style='font-size:15px; text-transform:uppercase;'>" . htmlspecialchars($r['nombre'] . ' ' . $r['apellidos']) . "</strong><br>";
                echo "<span style='font-size:13px; color:#555;'>CURP: " . htmlspecialchars($r['curp'] ?? '-') . "</span><br>";
                echo "<span  style='font-size:13px;'>Estatus:</span> <span style='font-size:13px; color:$color; font-weight:bold;'>" . htmlspecialchars($estatusRegistro) . "</span>";
                echo "</div>";
                echo "</li>";
            }
            echo "</ul>";
            echo "<p style='margin-top:10px;' text-decoratio:bold;>Total $nivel: (" . count($susRegistros) . ")</p>";
        } else {
            echo "<p>No tiene registros en este nivel.</p>";
        }
        ?>
    </div>






    <a href="dashboard.php" class="btn-volver">Volver</a>

    <!-- ==============================
       SCRIPT PARA ACTIVAR/DESACTIVAR EDICIÓN
    =============================== -->
    <script>
        const editarSwitch = document.getElementById('editarSwitch');
        const btnGuardar = document.getElementById('btnGuardar');

        editarSwitch.addEventListener('change', () => {
            const editMode = editarSwitch.checked;

            // Mostrar u ocultar el botón según el estado del switch
            btnGuardar.style.display = editMode ? 'block' : 'none';

            const elements = document.querySelectorAll('.editable');

            elements.forEach(el => {
                const type = el.dataset.type;

                if (editMode) {
                    // Activar edición
                    if (type === 'text') {
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.value = el.textContent.trim();
                        input.style.width = '100%';
                        input.style.fontSize = '14px';
                        input.style.textTransform = 'uppercase';
                        input.style.border = '1px solid #ccc';
                        input.style.borderRadius = '6px';
                        input.style.padding = '4px';
                        el.replaceWith(input);
                        input.classList.add('editable');
                        input.dataset.type = 'text';
                    } else if (type === 'textarea') {
                        const textarea = document.createElement('textarea');
                        textarea.value = el.textContent.trim();
                        textarea.style.width = '100%';
                        textarea.style.fontSize = '14px';
                        textarea.style.textTransform = 'uppercase';
                        textarea.style.border = '1px solid #ccc';
                        textarea.style.borderRadius = '6px';
                        textarea.style.padding = '6px';
                        textarea.style.resize = 'none';
                        el.replaceWith(textarea);
                        textarea.classList.add('editable');
                        textarea.dataset.type = 'textarea';
                    }
                } else {
                    // Desactivar edición
                    let newEl;
                    if (type === 'text') {
                        newEl = document.createElement('span');
                        newEl.textContent = el.value.trim();
                    } else if (type === 'textarea') {
                        newEl = document.createElement('div');
                        newEl.textContent = el.value.trim();
                        newEl.style.width = '100%';
                        newEl.style.fontSize = '14px';
                        newEl.style.textTransform = 'uppercase';
                        newEl.style.border = '1px solid #ccc';
                        newEl.style.padding = '6px';
                        newEl.style.borderRadius = '6px';
                        newEl.style.background = '#f9f9f9';
                    }
                    newEl.classList.add('editable');
                    newEl.dataset.type = type;
                    el.replaceWith(newEl);
                }
            });
        });
    </script>
</body>

</html>