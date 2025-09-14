<?php
setcookie('login_usuario', '', time() - 3600, '/');
header('Content-Type: application/json');
echo json_encode(['success' => true]);
