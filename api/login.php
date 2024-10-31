<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../controllers/UserController.php';

// Read raw POST data
$rawData = file_get_contents("php://input");

// Decode JSON data into PHP associative array
$data = json_decode($rawData, true);

// Check if email and password are set in the request
if (isset($data['email']) && isset($data['password'])) {
    // Call the login function in the controller
    $controller = new UserController();
    $response = $controller->login($data['email'], $data['password']);

    // Return the response in JSON format
    echo json_encode($response);
} else {
    // If email or password are missing, return an error
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and password are required'
    ]);
}
