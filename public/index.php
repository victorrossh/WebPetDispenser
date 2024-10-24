<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load the environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Get the requested URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files if the request is not an API call
if (preg_match('/^\/api\//', $requestUri)) {
    // API routing
    switch ($requestUri) {
        case '/api/register':
            require __DIR__ . '/../api/register.php';
            break;

        case '/api/login':
            require __DIR__ . '/../api/login.php';
            break;

        case '/api/command':
            require __DIR__ . '/../api/command.php';
            break;

        case '/api/user':
            require __DIR__ . '/../api/user.php';
            break;
        
        case '/api/device':
            require __DIR__ . '/../api/device.php';
            break;
        
        case '/api/registerdevice':
            require __DIR__ . '/../api/registerdevice.php';
            break;

        default:
            // 404 Not Found for undefined API routes
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'API Endpoint not found'
            ]);
            break;
    }
} else {
    // Serve the frontend files from the public directory
    $publicDir = __DIR__ . '/'; // Path to the public folder

    // Handle requests for specific files (e.g., CSS, JS, images)
    if ($requestUri !== '/' && file_exists($publicDir . $requestUri)) {
        // Serve the requested file
        return false; // Let PHP's built-in server handle the file
    }

    // Serve index.html by default (Single Page Application or main entry point)
    require $publicDir . 'index.html';
}
