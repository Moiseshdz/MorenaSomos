<?php
declare(strict_types=1);

/**
 * Normaliza una fecha recibida en componentes independientes.
 */
function normalizarFecha(?string $dia, ?string $mes, ?string $anio): ?string
{
    $dia = $dia !== null ? trim($dia) : '';
    $mes = $mes !== null ? trim($mes) : '';
    $anio = $anio !== null ? trim($anio) : '';

    if ($dia === '' || $mes === '' || $anio === '') {
        return null;
    }

    if (!ctype_digit($dia) || !ctype_digit($mes) || !ctype_digit($anio)) {
        return null;
    }

    $fecha = sprintf('%04d-%02d-%02d', (int) $anio, (int) $mes, (int) $dia);
    $dt = \DateTime::createFromFormat('Y-m-d', $fecha);

    if ($dt === false || $dt->format('Y-m-d') !== $fecha) {
        return null;
    }

    return $fecha;
}

/**
 * Maneja el guardado de la fotografía del afiliado.
 */
function guardarFotoAfiliado(array $archivo, string $curp): array
{
    if (empty($archivo) || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [true, '', ''];
    }

    if (($archivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return [false, 'Error al subir la foto', ''];
    }

    $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($archivo['name'] ?? '', PATHINFO_EXTENSION));

    if (!in_array($ext, $permitidas, true)) {
        return [false, 'Formato de foto no permitido', ''];
    }

    $uploadsDir = __DIR__ . '/uploads';
    if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0777, true) && !is_dir($uploadsDir)) {
        return [false, 'No se pudo crear el directorio de subidas', ''];
    }

    $nombreFoto = sprintf('%s_%s.%s', $curp, uniqid(), $ext);
    $destino = $uploadsDir . '/' . $nombreFoto;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        return [false, 'No se pudo guardar la foto', ''];
    }

    return [true, '', $nombreFoto];
}

/**
 * Devuelve la información resumida de un afiliado.
 */
function obtenerAfiliadoPorCurp(mysqli $conn, string $curp): ?array
{
    if ($curp === '') {
        return null;
    }

    $stmt = $conn->prepare('SELECT id, curp, nombre, apellidos, rol, curp_id_coordinador, curp_id_lider, curp_id_sublider FROM afiliados WHERE curp = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('s', $curp);
    $stmt->execute();
    $res = $stmt->get_result();
    $afiliado = $res && $res->num_rows === 1 ? $res->fetch_assoc() : null;
    $stmt->close();

    return $afiliado ?: null;
}

/**
 * Devuelve un pequeño resumen para mostrar relaciones jerárquicas.
 */
function obtenerResumenRelacion(mysqli $conn, ?string $curp): ?array
{
    $curp = $curp !== null ? trim($curp) : '';
    if ($curp === '') {
        return null;
    }

    $stmt = $conn->prepare('SELECT nombre, apellidos, curp FROM afiliados WHERE curp = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('s', $curp);
    $stmt->execute();
    $res = $stmt->get_result();
    $dato = $res && $res->num_rows === 1 ? $res->fetch_assoc() : null;
    $stmt->close();

    return $dato ?: null;
}

/**
 * Registra un nuevo afiliado en la base de datos.
 */
function registrarAfiliado(mysqli $conn, array $input, array $files, int $usuarioId, string $usuarioCurp): array
{
    $curp = strtoupper(trim($input['curp'] ?? ''));
    $nombre = trim($input['nombre'] ?? '');
    $apellidos = trim($input['apellidos'] ?? ($input['apellido'] ?? ''));
    $sexo = strtoupper(trim($input['sexo'] ?? ''));
    $estado = trim($input['estado'] ?? '');
    $telefono = trim($input['telefono'] ?? '');
    $domicilio = trim($input['domicilio'] ?? '');
    $seccion = trim($input['seccion'] ?? '');

    $dia = $input['dia'] ?? ($input['dia-nac'] ?? null);
    $mes = $input['mes'] ?? null;
    $anio = $input['anio'] ?? ($input['anio-nac'] ?? ($input['anios'] ?? null));
    $nacimiento = normalizarFecha($dia, $mes, $anio);

    if ($curp === '' || $nombre === '' || $apellidos === '' || $nacimiento === null || $sexo === '' || $estado === '' || $domicilio === '') {
        return ['success' => false, 'message' => 'Faltan datos obligatorios'];
    }

    if (!preg_match('/^[A-Z0-9]{18}$/', $curp)) {
        return ['success' => false, 'message' => 'CURP inválida'];
    }

    if (!in_array($sexo, ['M', 'F'], true)) {
        return ['success' => false, 'message' => 'Sexo inválido'];
    }

    $stmt = $conn->prepare('SELECT id FROM afiliados WHERE curp = ? LIMIT 1');
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error al validar la CURP'];
    }
    $stmt->bind_param('s', $curp);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'El afiliado ya se encuentra registrado'];
    }
    $stmt->close();

    [$fotoOk, $fotoMensaje, $fotoNombre] = guardarFotoAfiliado($files['foto'] ?? [], $curp);
    if (!$fotoOk) {
        return ['success' => false, 'message' => $fotoMensaje];
    }

    $rol = strtolower(trim($input['rol'] ?? ''));
    $curpCoordinador = strtoupper(trim($input['curp_id_coordinador'] ?? ''));
    $curpLider = strtoupper(trim($input['curp_id_lider'] ?? ''));
    $curpSublider = strtoupper(trim($input['curp_id_sublider'] ?? ''));

    $registrante = obtenerAfiliadoPorCurp($conn, strtoupper($usuarioCurp));

    if ($rol === '') {
        if ($registrante) {
            $rolRegistrante = strtolower($registrante['rol'] ?? '');
            switch ($rolRegistrante) {
                case 'coordinador':
                    $rol = 'lider';
                    break;
                case 'lider':
                    $rol = 'sublider';
                    break;
                case 'sublider':
                    $rol = 'afiliado';
                    break;
                default:
                    $rol = 'afiliado';
                    break;
            }
        } else {
            $rol = 'afiliado';
        }
    }

    if ($registrante) {
        $curpCoordinador = $curpCoordinador !== '' ? $curpCoordinador : ($registrante['curp_id_coordinador'] ?? ($registrante['rol'] === 'coordinador' ? $registrante['curp'] : null));
        $curpLider = $curpLider !== '' ? $curpLider : ($registrante['curp_id_lider'] ?? ($registrante['rol'] === 'lider' ? $registrante['curp'] : null));
        $curpSublider = $curpSublider !== '' ? $curpSublider : ($registrante['curp_id_sublider'] ?? ($registrante['rol'] === 'sublider' ? $registrante['curp'] : null));

        if ($registrante['rol'] === 'coordinador' && $rol === 'lider') {
            $curpCoordinador = $registrante['curp'];
        }
        if ($registrante['rol'] === 'lider' && in_array($rol, ['sublider', 'afiliado'], true)) {
            $curpLider = $registrante['curp'];
            $curpCoordinador = $curpCoordinador !== '' ? $curpCoordinador : ($registrante['curp_id_coordinador'] ?? null);
        }
        if ($registrante['rol'] === 'sublider' && $rol === 'afiliado') {
            $curpSublider = $registrante['curp'];
            $curpLider = $curpLider !== '' ? $curpLider : ($registrante['curp_id_lider'] ?? null);
            $curpCoordinador = $curpCoordinador !== '' ? $curpCoordinador : ($registrante['curp_id_coordinador'] ?? null);
        }
    }

    $sql = 'INSERT INTO afiliados (curp, nombre, apellidos, nacimiento, sexo, estado, telefono, domicilio, seccion, foto, estatus, registrado_en, usuario_id, rol, curp_id_coordinador, curp_id_lider, curp_id_sublider)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "activo", NOW(), ?, ?, ?, ?, ?)';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error al preparar el registro del afiliado'];
    }

    $telefono = $telefono !== '' ? $telefono : null;
    $seccion = $seccion !== '' ? $seccion : null;
    $fotoNombre = $fotoNombre !== '' ? $fotoNombre : null;
    $curpCoordinador = $curpCoordinador !== '' ? $curpCoordinador : null;
    $curpLider = $curpLider !== '' ? $curpLider : null;
    $curpSublider = $curpSublider !== '' ? $curpSublider : null;

    $stmt->bind_param(
        'ssssssssssissss',
        $curp,
        $nombre,
        $apellidos,
        $nacimiento,
        $sexo,
        $estado,
        $telefono,
        $domicilio,
        $seccion,
        $fotoNombre,
        $usuarioId,
        $rol,
        $curpCoordinador,
        $curpLider,
        $curpSublider
    );

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Afiliado registrado correctamente'];
    }

    $mensaje = $conn->error !== '' ? $conn->error : 'No se pudo guardar el afiliado';
    $stmt->close();

    return ['success' => false, 'message' => $mensaje];
}
