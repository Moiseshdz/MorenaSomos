<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$curp = isset($_POST['curp']) ? strtoupper(trim($_POST['curp'])) : '';
$response = ['exists' => false];

if ($curp !== '' && preg_match('/^[A-Z0-9]{18}$/', $curp)) {
    $stmt = $conn->prepare('SELECT curp FROM afiliados WHERE curp = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('s', $curp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response['exists'] = true;
        }
        $stmt->close();
    }
}

$conn->close();
echo json_encode($response);
