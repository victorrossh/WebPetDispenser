<?php
class User {
    private $conn;

    public function __construct() {
        // Load the database configuration
        $dbConfig = require_once __DIR__ . '/../config/database.php';

        // Create a connection using the loaded config
        $this->conn = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database'], $dbConfig['port']);

        if ($this->conn->connect_error) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
    }

    public function create($data) {
        return "test";
    }

    public function login($data) {
        return "test";
    }
}
