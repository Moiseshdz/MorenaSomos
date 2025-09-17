<?php
require_once __DIR__ . '/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die('<p>No se especificó un afiliado válido.</p>');
}

$stmt = $conn->prepare('SELECT * FROM afiliados WHERE id = ? LIMIT 1');
if (!$stmt) {
    $conn->close();
    die('<p>Error al consultar el afiliado.</p>');
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $stmt->close();
    $conn->close();
    die('<p>Afiliado no encontrado.</p>');
}

$stmt->close();
$conn->close();

$nombreCompleto = htmlspecialchars(($row['nombre'] ?? '') . ' ' . ($row['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
$foto = !empty($row['foto']) ? '../php/uploads/' . htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8') : '../src/avatar.jpg';
$sexo = htmlspecialchars($row['sexo'] ?? '', ENT_QUOTES, 'UTF-8');
$estado = htmlspecialchars($row['estado'] ?? '', ENT_QUOTES, 'UTF-8');
$domicilio = htmlspecialchars($row['domicilio'] ?? '', ENT_QUOTES, 'UTF-8');
$seccion = htmlspecialchars($row['seccion'] ?? '', ENT_QUOTES, 'UTF-8');
$estatus = htmlspecialchars($row['estatus'] ?? '', ENT_QUOTES, 'UTF-8');
$registrado = htmlspecialchars($row['registrado_en'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="dashboard-container">
    <div class="card-welcome">
        <img src="<?= $foto ?>" alt="Foto del afiliado">
        <h2><?= $nombreCompleto ?></h2>
        <p><?= htmlspecialchars($row['curp'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

    <div class="perfil-card">
        <h3>📄 Datos del Afiliado</h3>
        <div class="perfil-info">
            <h1 id="vistatext"><span>Sexo:</span> <?= $sexo ?></h1>
            <h1 id="vistatext"><span>Estado:</span> <?= $estado ?></h1>
            <h1 id="vistatext"><span>Domicilio:</span> <?= $domicilio ?></h1>
            <h1 id="vistatext"><span>Sección:</span> <?= $seccion ?></h1>
            <h1 id="vistatext"><span>Estatus:</span> <?= ucfirst($estatus) ?></h1>
            <?php if ($registrado !== ''): ?>
                <h1 id="vistatext"><span>Registrado en:</span> <?= $registrado ?></h1>
            <?php endif; ?>
        </div>
    </div>
    <br><br>

    <div class="bottom-nav">
        <button onclick="window.location.href='dashboard.html'">
            <i class="fas fa-home"></i>
            Inicio
        </button>
        <button onclick="window.location.href='editar.php?id=<?= $id ?>'">
            <i class="fas fa-edit"></i>
            Editar
        </button>
        <button onclick="if(confirm('¿Seguro que deseas eliminar este registro?')){ window.location.href='borrar.php?id=<?= $id ?>'; }">
            <i class="fas fa-trash"></i>
            Borrar
        </button>
    </div>
</div>

<style>
.perfil-card {
    background: #fff;
    margin: 20px auto;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-width: 500px;
}

.perfil-card h3 {
    text-align: center;
    color: #611232;
    margin-bottom: 20px;
}

.perfil-info p {
    margin: 10px 0;
    font-size: 15px;
    color: #333;
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
}

.perfil-info p:last-child {
    border-bottom: none;
}

.perfil-info span {
    font-weight: bold;
    color: #611232;
    display: inline-block;
    width: 140px;
}

#vistatext{
    font-size: 20px;
}
</style>
