<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/registro_handler.php';

if (!isset($_SESSION['usuario_id'], $_SESSION['usuario_curp'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$resultado = registrarAfiliado(
    $conn,
    $_POST,
    $_FILES,
    (int) $_SESSION['usuario_id'],
    (string) $_SESSION['usuario_curp']
);

echo json_encode($resultado);
$conn->close();
