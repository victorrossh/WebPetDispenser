<?php
require_once '../models/User.php';

class RegisterController {
    public function register($data) {
        $user = new User();
        if ($user->create($data)) {
            return ['status' => 'success', 'message' => 'User registered successfully'];
        }
        return ['status' => 'error', 'message' => 'Failed to register user'];
    }
}