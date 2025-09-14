<?php
include "conexion.php";

$curp = $_POST['curp'];
$response = ['exists' => false];

$stmt = $conn->prepare("SELECT curp FROM afiliados WHERE curp = ?");
$stmt->bind_param("s", $curp);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0){
    $response['exists'] = true;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
