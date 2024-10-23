<?php
require_once '../models/User.php';

class LoginController {
    public function login($data) {
        $user = new User();
		$token = $user->login($data);
        if ($token) {
            return ['status' => 'success', 'message' => 'User logged in successfully', 'token' => $token];
        }
        return ['status' => 'error', 'message' => 'Failed to login user'];
    }
}