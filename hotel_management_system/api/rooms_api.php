<?php
// Включите отладку
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// ПРАВИЛЬНЫЕ ПУТИ - должны вести на уровень выше
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Остальной код без изменений...

// Разрешить CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
        
        $type_id = isset($_GET['type']) ? (int)$_GET['type'] : 0;
        $check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
        $check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
        
        if ($type_id && $check_in && $check_out) {
            // Проверка валидности дат
            if (strtotime($check_in) === false || strtotime($check_out) === false || 
                strtotime($check_in) >= strtotime($check_out)) {
                $response['error'] = 'Invalid date range';
            } else {
                $rooms = getAvailableRooms($type_id, $check_in, $check_out);
                $response = [
                    'success' => true,
                    'rooms' => $rooms
                ];
            }
        } else {
            $response['error'] = 'Missing required parameters';
        }
    } else {
        $response['error'] = 'Invalid request method';
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    $response['error'] = 'Internal server error';
}

echo json_encode($response);
?>