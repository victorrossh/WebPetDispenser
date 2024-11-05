<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once "../controllers/DeviceController.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Check if device token is set in the request
    if (isset($_GET["token"])) {
        // Call the getDevice function in the controller
        $controller = new DeviceController();
        $response = $controller->getDevice($_GET["token"]);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token is missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Token and Device Id are required",
        ]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Read raw POST data
    $rawData = file_get_contents("php://input");

    // Decode JSON data into PHP associative array
    $data = json_decode($rawData, true);
    // Check if token and name are set in the request
    if (isset($data["token"]) && isset($data["name"])) {
        // Call the getdata function in the controller
        $controller = new DeviceController();
        $response = $controller->create($data["token"], isset($data["name"]));

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token or name are missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Token and Name are required",
        ]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    // Read raw POST data
    $rawData = file_get_contents("php://input");

    // Decode JSON data into PHP associative array
    $data = json_decode($rawData, true);
    // Check if token and name are set in the request
    if (isset($data["token"]) && isset($data["deviceId"])) {
        // Call the delete function in the controller
        $controller = new DeviceController();
        $response = $controller->delete($data["token"], $data["deviceId"]);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token or deviceId are missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Token and DeviceID are required",
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
