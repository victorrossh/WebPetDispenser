<?php
class Device {
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
        // Prepare the SQL query to get the user ID from the session token
        $sql = "SELECT u.id 
                FROM Users u 
                WHERE u.id = (SELECT UserId FROM Sessions s WHERE s.token = ?)";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
        
        // Bind the user's token to the placeholder
        $stmt->bind_param("s", $data["token"]);
        
        // Execute the query
        $stmt->execute();
        
        // Get the result set
        $result = $stmt->get_result();
        
        // Check if a user with the provided token exists
        if ($result->num_rows > 0) {
            // Fetch the user ID
            $user = $result->fetch_assoc();
            $userId = $user['id'];
    
            // Generate a unique token for the device
            $deviceToken = bin2hex(random_bytes(16));
    
            // Prepare the SQL query to insert the new device
            $sql = "INSERT INTO Devices (name, owner, token) VALUES (?, ?, ?)";
    
            // Prepare the statement
            $stmt = $this->conn->prepare($sql);
            
            // Bind the parameters (device name, user ID, device token)
            $stmt->bind_param("sis", $data["name"], $userId, $deviceToken);
            
            // Execute the query and check if the insertion was successful
            if ($stmt->execute()) {
                // If rows were affected, the device was inserted
                if ($stmt->affected_rows > 0) {
                    // Close the statement
                    $stmt->close();
                    return array('status' => 'success', 'message' => 'Device created successfully', 'deviceToken' => $deviceToken);
                } else {
                    // If no rows were affected, there was an error
                    $stmt->close();
                    return array('status' => 'error', 'message' => 'Failed to create device');
                }
            } else {
                // Close the statement and return error
                $stmt->close();
                return array('status' => 'error', 'message' => 'Error creating device');
            }
        } else {
            // No user found with the provided token
            $stmt->close();
            return array('status' => 'error', 'message' => 'Invalid user token');
        }
    }
    

    public function getdata($data) {
        // Prepare the SQL query to select the user by login token
        $sql = "SELECT u.id, u.name, u.email, u.admin 
                FROM Users u 
                WHERE u.id = (SELECT UserId FROM Sessions s WHERE s.token = ?)";
        
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
            $user = $result->fetch_assoc();
    
            // Prepare the SQL query to check if the device belongs to the user
            $sql = "SELECT d.id, d.name 
                    FROM Devices d 
                    WHERE d.id = ? AND d.owner = ?";
    
            // Prepare the statement
            $stmt = $this->conn->prepare($sql);
    
            // Bind the device ID and user ID to the placeholders
            $stmt->bind_param("ii", $data["deviceId"], $user["id"]);
            
            // Execute the query
            $stmt->execute();
            
            // Get the result set
            $result = $stmt->get_result();
            
            // Check if the device exists and belongs to the user
            if ($result->num_rows > 0) {
                $device = $result->fetch_assoc();
    
                // Fetch additional information from the DeviceQueue for this device
                $sql = "SELECT * FROM DeviceQueue WHERE DeviceId = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $device['id']);
                $stmt->execute();
                $queueResult = $stmt->get_result();
    
                // Fetch the queue data for the device
                $queueData = [];
                while ($queueRow = $queueResult->fetch_assoc()) {
                    $queueData[] = $queueRow;
                }
    
                // Return success response with device and queue information
                $stmt->close();
                return array(
                    'status' => 'success', 
                    'device' => $device, 
                    'queue' => $queueData
                );
            } else {
                // No device found or does not belong to the user
                $stmt->close();
                return array('status' => 'error', 'message' => 'No device found or unauthorized access');
            }
        } else {
            // No user found with the provided token
            $stmt->close();
            return array('status' => 'error', 'message' => 'Invalid Token');
        }
    }
    
    public function addCommandToQueue($data) {
        // Prepare the SQL query to select the user by login token
        $sql = "SELECT u.id 
                FROM Users u 
                WHERE u.id = (SELECT UserId FROM Sessions s WHERE s.token = ?)";
        
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
            $user = $result->fetch_assoc();
    
            // Prepare the SQL query to check if the device belongs to the user
            $sql = "SELECT d.id 
                    FROM Devices d 
                    WHERE d.id = ? AND d.owner = ?";
    
            // Prepare the statement
            $stmt = $this->conn->prepare($sql);
    
            // Bind the device ID and user ID to the placeholders
            $stmt->bind_param("ii", $data["deviceId"], $user["id"]);
            
            // Execute the query
            $stmt->execute();
            
            // Get the result set
            $result = $stmt->get_result();
            
            // Check if the device exists and belongs to the user
            if ($result->num_rows > 0) {
                // The device belongs to the user, now insert the command into the queue
                $device = $result->fetch_assoc();
    
                // Prepare the SQL query to insert the command into DeviceQueue
                $sql = "INSERT INTO DeviceQueue (DeviceId, command, issuedOn) VALUES (?, ?, NOW())";
                
                // Prepare the statement
                $stmt = $this->conn->prepare($sql);
                
                // Bind the device ID and the command to the placeholders
                $stmt->bind_param("is", $device["id"], $data["command"]);
                
                // Execute the insert query
                if ($stmt->execute()) {
                    // Success, command was added to the queue
                    $stmt->close();
                    return array('status' => 'success', 'message' => 'Command added to queue');
                } else {
                    // Failed to insert command
                    $stmt->close();
                    return array('status' => 'error', 'message' => 'Failed to add command to queue');
                }
            } else {
                // No device found or unauthorized access
                $stmt->close();
                return array('status' => 'error', 'message' => 'No device found or unauthorized access');
            }
        } else {
            // No user found with the provided token
            $stmt->close();
            return array('status' => 'error', 'message' => 'Invalid Token');
        }
    }

    public function executeCommandFromQueue($deviceToken) {
        // Prepare the SQL query to select the device by its token
        $sql = "SELECT id 
                FROM Devices 
                WHERE token = ?";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
        
        // Bind the device token to the placeholder
        $stmt->bind_param("s", $deviceToken);
        
        // Execute the query
        $stmt->execute();
        
        // Get the result set
        $result = $stmt->get_result();
        
        // Check if a device with the provided token exists
        if ($result->num_rows > 0) {
            // Fetch the device ID
            $device = $result->fetch_assoc();
            $deviceId = $device['id'];
    
            // Prepare the SQL query to select the oldest unexecuted command for the device
            $sql = "SELECT * 
                    FROM DeviceQueue 
                    WHERE DeviceId = ? AND executedOn IS NULL 
                    ORDER BY issuedOn ASC 
                    LIMIT 1";
            
            // Prepare the statement
            $stmt = $this->conn->prepare($sql);
            
            // Bind the device ID to the placeholder
            $stmt->bind_param("i", $deviceId);
            
            // Execute the query
            $stmt->execute();
            
            // Get the result set
            $result = $stmt->get_result();
            
            // Check if there is any unexecuted command
            if ($result->num_rows > 0) {
                // Fetch the oldest unexecuted command
                $command = $result->fetch_assoc();
    
                // Prepare the SQL query to update the command and mark it as executed
                $sql = "UPDATE DeviceQueue 
                        SET executedOn = NOW() 
                        WHERE id = ?";
                
                // Prepare the statement
                $stmt = $this->conn->prepare($sql);
                
                // Bind the command ID to the placeholder
                $stmt->bind_param("i", $command["id"]);
                
                // Execute the update query
                if ($stmt->execute()) {
                    // Success, command marked as executed
                    $stmt->close();
                    return array(
                        'status' => 'success', 
                        'message' => 'Command executed', 
                        'command' => $command
                    );
                } else {
                    // Failed to update the command
                    $stmt->close();
                    return array('status' => 'error', 'message' => 'Failed to mark command as executed');
                }
            } else {
                // No unexecuted commands found
                $stmt->close();
                return array('status' => 'error', 'message' => 'No unexecuted commands found');
            }
        } else {
            // No device found with the provided token
            $stmt->close();
            return array('status' => 'error', 'message' => 'Invalid device token');
        }
    }
    
    
    
}
