<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../controllers/UserController.php';

$controller = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get token
    $token = $_GET["token"];

    if ($token) {
        // Check login status with the token
        $response = $controller->getLogin($token);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token is missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => 'Token is required'
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read raw POST data
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    // Check if email and password are set in the request
    if (isset($data['email']) && isset($data['password'])) {
        // Call the login function in the controller
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
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
?>
