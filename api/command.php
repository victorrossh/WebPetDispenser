<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../controllers/DeviceController.php';

// Instantiate the controller
$controller = new DeviceController();

// Check the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request - Add command to queue
    // Read raw POST data
    $rawData = file_get_contents("php://input");

    // Decode JSON data into PHP associative array
    $data = json_decode($rawData, true);

    // Check if token, deviceId, command and info are set in the request
    if (isset($data['token']) && isset($data['deviceId']) && isset($data['command']) && isset($data['info'])) {
        // Call the addCommandToQueue function in the controller
        $response = $controller->addCommandToQueue($data['token'], $data['deviceId'], $data['command'], $data['info']);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token, deviceId, command or info are missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => 'Token, Device Id and Command are required'
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request - Execute the oldest command
    // Check if token is provided as a query parameter
    if (isset($_GET['token'])) {
        // Get the token from the query string
        $deviceToken = $_GET['token'];

        // Call the executeCommandFromQueue function in the controller
        $response = $controller->executeCommandFromQueue($deviceToken);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token is missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => 'Device token is required'
        ]);
    }
} else {
    // If the request method is not GET or POST, return an error
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
