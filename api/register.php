<?php
require_once '../controllers/RegisterController.php';

// Read raw POST data
$rawData = file_get_contents("php://input");

// Decode JSON data into PHP associative array
$data = json_decode($rawData, true);

// Check if name, password and email are set in the request
if (isset($data['name']) && isset($data['password']) && isset($data['email'])) {
    // Call the register function in the controller
    $controller = new RegisterController();
    $response = $controller->create($data);

    // Return the response in JSON format
    echo json_encode($response);
} else {
    // If name, password or email are missing, return an error
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'message' => 'Name, password and email are required'
    ]);
}
