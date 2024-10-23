<?php
require_once '../models/User.php';

class LoginController {
    public function login($data) {
        $user = new User();
		$response = $user->login($data);
        if ($response) {
            return ['status' => 'success', 'message' => 'User logged in successfully', 'name' => $response["name"], 'token' => $response["token"]];
        }
        return ['status' => 'error', 'message' => 'Failed to login user'];
    }
}