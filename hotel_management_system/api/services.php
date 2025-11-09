<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $services = getAllActiveServices();
        $response = [
            'success' => true,
            'services' => $services
        ];
    } else {
        $response['error'] = 'Invalid request method';
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>