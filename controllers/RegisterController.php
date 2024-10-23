<?php
require_once '../models/User.php';

class RegisterController {
    public function create($data) {
        $user = new User();
        $response = $user->create($data);
        return $response;
    }
}