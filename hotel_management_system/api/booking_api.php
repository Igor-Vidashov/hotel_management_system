<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            throw new Exception('Invalid input data');
        }
        
        $guest_data = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'passport' => $data['passport']
        ];
        
        $booking_data = [
            'room_id' => $data['room_id'],
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'adults' => $data['adults'],
            'children' => $data['children'],
            'total_amount' => $data['total_amount']
        ];
        
        $result = createBooking($guest_data, $booking_data);
        
        if ($result['success']) {
            $response = [
                'success' => true,
                'booking_id' => $result['booking_id'],
                'redirect_url' => '/booking-confirmation.php?id=' . $result['booking_id']
            ];
        } else {
            $response['error'] = $result['error'];
        }
    } else {
        $response['error'] = 'Invalid request method';
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>