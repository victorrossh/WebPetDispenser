<?php
require_once '../controllers/DeviceController.php';

// Read raw POST data
$rawData = file_get_contents("php://input");

// Decode JSON data into PHP associative array
$data = json_decode($rawData, true);

// Check if token and name are set in the request
if (isset($data['token']) && isset($data['name'])) {
    // Call the getdata function in the controller
    $controller = new DeviceController();
    $response = $controller->create($data);

    // Return the response in JSON format
    echo json_encode($response);
} else {
    // If token or name are missing, return an error
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'message' => 'Token and Name are required'
    ]);
}
