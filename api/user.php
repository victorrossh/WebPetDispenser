<?php
require_once '../controllers/UserController.php';

// Read raw POST data
$rawData = file_get_contents("php://input");

// Decode JSON data into PHP associative array
$data = json_decode($rawData, true);

// Check if token is set in the request
if (isset($data['token'])) {
    // Call the getdata function in the controller
    $controller = new UserController();
    $response = $controller->getdata($data);

    // Return the response in JSON format
    echo json_encode($response);
} else {
    // If token is missing, return an error
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'message' => 'Token required'
    ]);
}
