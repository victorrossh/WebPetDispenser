<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once "../controllers/UserController.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Check if token is set in the request
    if (isset($_GET["token"])) {
        // Call the getUser function in the controller
        $controller = new UserController();
        $response = $controller->getUser($_GET["token"]);

        // Return the response in JSON format
        echo json_encode($response);
    } else {
        // If token is missing, return an error
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Token required",
        ]);
    }
} else {
    // If the request method is not GET or POST, return an error
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed",
    ]);
}
