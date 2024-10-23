
<?php
require_once '../controllers/RegisterController.php';
header('Content-Type: application/json');

$controller = new RegisterController();
$response = $controller->register($_POST);
echo json_encode($response);