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
        // Prepare the SQL query to insert a new user (ignore duplicates based on unique constraints)
        $sql = "INSERT IGNORE INTO Users (name, email, password) VALUES (?, ?, ?)";
    
        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
    
        // Hash the password before storing it
        $hashedPassword = password_hash($data["password"], PASSWORD_BCRYPT);
    
        // Bind the parameters (name, email, password)
        $stmt->bind_param("sss", $data["name"], $data["email"], $hashedPassword);
    
        // Execute the query and check if the insertion was successful
        if ($stmt->execute()) {
            // If rows were affected, the user was inserted
            if ($stmt->affected_rows > 0) {
                // Close the statement and log the user in
                $stmt->close();
                return $this->login($data);
            } else {
                // If no rows were affected, the user already exists
                $stmt->close();
                return array('status' => 'error', 'message' => 'User already exists');
            }
        } else {
            $stmt->close();
            return array('status' => 'error', 'message' => 'Error creating user');
        }
    }
    
    public function login($data) {
        // Prepare the SQL query to select the user by email
        $sql = "SELECT id, name, password, admin FROM Users WHERE email = ?";
    
        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
    
        // Bind the email to the placeholder
        $stmt->bind_param("s", $data["email"]);
    
        // Execute the query
        $stmt->execute();
    
        // Get the result set
        $result = $stmt->get_result();
        
        // Check if a user with the provided email exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
    
            // Verify the provided password against the stored hash
            if (password_verify($data["password"], $row["password"])) {
                // Password is correct, proceed with login
                $stmt->close();
    
                // Generate a token and create a session
                $token = $this->generate_token(32);
                $this->create_session($row["id"], $token);
    
                // Return the user name and token
                return array(
                    'name' => $row["name"],
                    'token' => $token,
                    'admin' => $row["admin"]
                );
            } else {
                // Incorrect password
                $stmt->close();
                return array('status' => 'error', 'message' => 'Invalid password');
            }
        } else {
            // No user found with the provided email
            $stmt->close();
            return array('status' => 'error', 'message' => 'User not found');
        }
    }

    public function getdata($data) {
        // Prepare the SQL query to select the user by login token
        $sql = "SELECT u.id, u.name, u.email, u.admin FROM Users u WHERE u.id = (SELECT UserId From Sessions s WHERE s.token = ?)";
    
        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
    
        // Bind the token to the placeholder
        $stmt->bind_param("s", $data["token"]);
    
        // Execute the query
        $stmt->execute();
    
        // Get the result set
        $result = $stmt->get_result();
        
        // Check if a user with the provided token exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $sql = "SELECT * FROM Devices d WHERE d.owner = ?";

            // Prepare the statement
            $stmt = $this->conn->prepare($sql);
    
            // Bind the token to the placeholder
            $stmt->bind_param("s", $row["id"]);
    
            // Execute the query
            $stmt->execute();
    
            // Get the result set
            $result = $stmt->get_result();

            if($result->num_rows > 0) {
                // Fetch all devices and store them in an array
                $devices = [];
                while ($device = $result->fetch_assoc()) {
                    $devices[] = $device;
                }

                // Return success response with user and devices data
                $stmt->close();
                return array(
                    'status' => 'success', 
                    'user' => $row, 
                    'devices' => $devices
                );
            }
            else {
                // No devices found with the provided user
                $stmt->close();
                return array('status' => 'error', 'message' => 'No devices found with the provided user');
            }
    
        } else {
            // No user found with the provided token
            $stmt->close();
            return array('status' => 'error', 'message' => 'Invalid Token');
        }
    }

    function generate_token($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $token;
    }
    
    function create_session( $id, $token )
    {
        $sql = "INSERT INTO Sessions ( UserId, token, expireOn ) VALUES ( ?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY) )";

        // Prepare the statement
        $stmt = $this->conn->prepare($sql);

        // Bind the player's name to the placeholder
        $stmt->bind_param("ss", $id, $token);

        // Execute the query
        $stmt->execute();

        $stmt->close();
        $this->conn->close();
    }
}