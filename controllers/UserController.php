<?php
require_once '../models/User.php';

class UserController {
    public function create($data) {
        $user = new User();
        $response = $user->create($data);
        return $response;
    }

	public function login($data) {
        $user = new User();
		$response = $user->login($data);
        return $response;
    }

    public function getdata($data) {
        $user = new User();
		$response = $user->getdata($data);
        return $response;
    }
}