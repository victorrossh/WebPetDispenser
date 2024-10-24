<?php
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

    // Check if token, deviceId, command, info and time are set in the request
    if (isset($data['token']) && isset($data['deviceId']) && isset($data['command']) && isset($data['time']) && isset($data['info'])) {
        // Call the pushcommand function in the controller
        $response = $controller->createschedule($data);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token, deviceId, command, info or time are missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => 'Token, Device Id, Command and Time are required'
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request - Execute the oldest command
    // Check if token is provided as a query parameter
    if (isset($_GET['token'])) {
        // Get the token from the query string
        $deviceToken = $_GET['token'];

        // Call the executeschedule function in the controller
        $response = $controller->executeschedule($deviceToken);

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	// Handle DELETE request - Add command to queue
    // Read raw DELETE data
    $rawData = file_get_contents("php://input");

    // Decode JSON data into PHP associative array
    $data = json_decode($rawData, true);

    // Check if token, deviceId, command and time are set in the request
    if (isset($data['token']) && isset($data['deviceId']) && isset($data['scheduleId'])) {
        // Call the pushcommand function in the controller
        $response = $controller->deleteschedule($data);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token, deviceId, command or time are missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => 'Token, Device Id and Schedule Id are required'
        ]);
    }
}
else {
    // If the request method is not GET or POST, return an error
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
