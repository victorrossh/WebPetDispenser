<?php
require_once '../models/User.php';

class UserController {
    public function create($name, $email, $password) {
        $user = new User();
        $response = $user->create($name, $email, $password);
        return $response;
    }

	public function login($email, $password) {
        $user = new User();
		$response = $user->login($email, $password);
        return $response;
    }

    public function getUser($userToken) {
        $user = new User();
		$response = $user->getUser($userToken);
        return $response;
    }
}