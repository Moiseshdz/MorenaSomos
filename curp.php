<?php
// =============================
// CONFIGURACIÓN
// =============================
$token = 'dec17792-c01b-46ea-bcbf-9f70c0ecc1cb'; // tu token real
$curpIngresada = strtoupper($_POST['curp'] ?? '');
$confirmar = isset($_POST['confirmar']);
$errores = [];
$resultado = [];

// =============================
// VALIDACIÓN LOCAL (sin gastar)
// =============================

// Regex CURP oficial
$regexCurp = "/^[A-Z]{4}[0-9]{6}[HM]{1}[A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}$/";

if ($curpIngresada && !preg_match($regexCurp, $curpIngresada)) {
    $errores[] = "La CURP ingresada no tiene un formato válido.";
}

// =============================
// SOLO SI CONFIRMA → API REAL
// =============================
if ($confirmar && empty($errores)) {
    $endpoint = "https://api.valida-curp.com.mx/curp/obtener_datos/?token=" . urlencode($token) . "&curp=" . urlencode($curpIngresada);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => ['User-Agent: MiApp/1.0']
    ]);

    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($res === false || $httpCode >= 400) {
        $errores[] = "Error al consultar la API (HTTP $httpCode - $curlErr)";
    } else {
        $data = json_decode($res, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errores[] = "Respuesta inválida de la API.";
        } else {
            $resultado = $data['response']['Solicitante'] ?? [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta CURP Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Consulta CURP Segura</h4>
        </div>
        <div class="card-body">

            <!-- Mostrar errores -->
            <?php if ($errores): ?>
                <div class="alert alert-danger">
                    <?= implode("<br>", $errores) ?>
                </div>
            <?php endif; ?>

            <!-- Paso 1: ingresar CURP -->
            <?php if (!$curpIngresada): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Ingrese la CURP</label>
                        <input type="text" name="curp" maxlength="18" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Validar CURP</button>
                </form>
            <?php endif; ?>

            <!-- Paso 2: confirmación antes de gastar -->
            <?php if ($curpIngresada && !$confirmar && empty($errores)): ?>
                <div class="alert alert-warning">
                    ⚠️ Se va a consultar la CURP <strong><?= htmlspecialchars($curpIngresada) ?></strong>.<br>
                    Esta consulta tiene costo, ¿quieres continuar?
                </div>
                <form method="POST">
                    <input type="hidden" name="curp" value="<?= htmlspecialchars($curpIngresada) ?>">
                    <button type="submit" name="confirmar" value="1" class="btn btn-danger">Sí, consultar</button>
                    <a href="" class="btn btn-secondary">Cancelar</a>
                </form>
            <?php endif; ?>

            <!-- Paso 3: resultados de la API -->
            <?php if ($resultado): ?>
                <h5 class="mt-3">Resultado</h5>
                <table class="table table-bordered">
                    <tr><th>CURP</th><td><?= htmlspecialchars($resultado['CURP'] ?? '') ?></td></tr>
                    <tr><th>Nombre(s)</th><td><?= htmlspecialchars($resultado['Nombres'] ?? '') ?></td></tr>
                    <tr><th>Apellido Paterno</th><td><?= htmlspecialchars($resultado['ApellidoPaterno'] ?? '') ?></td></tr>
                    <tr><th>Apellido Materno</th><td><?= htmlspecialchars($resultado['ApellidoMaterno'] ?? '') ?></td></tr>
                    <tr><th>Fecha Nacimiento</th><td><?= htmlspecialchars($resultado['FechaNacimiento'] ?? '') ?></td></tr>
                    <tr><th>Sexo</th><td><?= htmlspecialchars($resultado['Sexo'] ?? '') ?></td></tr>
                    <tr><th>Entidad</th><td><?= htmlspecialchars($resultado['EntidadNacimiento'] ?? '') ?></td></tr>
                    <tr><th>Estatus</th><td><span class="badge bg-info"><?= htmlspecialchars($resultado['StatusCurp'] ?? '') ?></span></td></tr>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>
