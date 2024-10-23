
<?php
require_once '../controllers/LoginController.php';

$controller = new LoginController();
$response = $controller->login($_POST);
echo json_encode($response);