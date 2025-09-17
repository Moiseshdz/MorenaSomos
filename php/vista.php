<?php
include 'conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<p>No se especificó un afiliado válido.</p>");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM afiliados WHERE id = $id LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("<p>Afiliado no encontrado.</p>");
}

$conn->close();
?>

<div class="dashboard-container">

    <!-- Cabecera estilo tarjeta -->
    <div class="card-welcome">
        <?php if (!empty($row['foto'])): ?>
            <img src="../php/uploads/<?php echo $row['foto']; ?>" alt="Foto del afiliado">
        <?php else: ?>
            <img src="../assets/avatar.png" alt="Sin foto">
        <?php endif; ?>
        <h2><?php echo $row['nombre'] . " " . $row['apellidos']; ?></h2>
        <p><?php echo $row['curp']; ?></p>
    </div>

    <!-- Ficha de perfil -->
    <div class="perfil-card">
        <h3>📄 Datos del Afiliado</h3>
        <div class="perfil-info">
            <!--h1 id="vistatext"><span>Nacimiento:</span> <?php# echo $row['dia'].'/'.$row['mes'].'/'.$row['anios']; ?></h1-->
            <h1 id="vistatext"><span>Sexo:</span> <?php echo strtoupper($row['sexo']); ?></h1>
            <h1 id="vistatext"><span>Estado:</span> <?php echo $row['estado']; ?></h1>
            <h1 id="vistatext"><span>Domicilio:</span> <?php echo $row['domicilio']; ?></h1>
            <h1 id="vistatext"><span>Sección:</span> <?php echo $row['seccion']; ?></h1>
            <h1 id="vistatext"><span>Estatus:</span> <?php echo ucfirst($row['estatus']); ?></h1>
            <h1 id="vistatext"><span>Registrado en:</span> <?php echo $row['registrado_el']; ?></h1>
        </div>
    </div>
    <br><br>

    <!-- Navegación inferior -->
    <div class="bottom-nav">
        <button onclick="window.location.href='dashboard.html'">
            <i class="fas fa-home"></i>
            Inicio
        </button>
        <button onclick="window.location.href='editar.php?id=<?php echo $row['id']; ?>'">
            <i class="fas fa-edit"></i>
            Editar
        </button>
        <button onclick="if(confirm('¿Seguro que deseas eliminar este registro?')){ window.location.href='borrar.php?id=<?php echo $row['id']; ?>'; }">
            <i class="fas fa-trash"></i>
            Borrar
        </button>
    </div>

</div>

<style>
/* Ficha estilo documento */
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
