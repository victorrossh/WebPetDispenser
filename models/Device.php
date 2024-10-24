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
        // Get the user ID from the session token
        $userId = $this->getUserIdFromToken($data['token']);
        
        if (!$userId) {
            // Invalid token or session expired
            return array('status' => 'error', 'message' => 'Invalid user token');
        }
    
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
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return array('status' => 'success', 'message' => 'Device created successfully', 'deviceToken' => $deviceToken);
            } else {
                $stmt->close();
                return array('status' => 'error', 'message' => 'Failed to create device');
            }
        } else {
            $stmt->close();
            return array('status' => 'error', 'message' => 'Error creating device');
        }
    }
      
    public function getdata($data) {
        // Get the user ID from the session token
        $userId = $this->getUserIdFromToken($data['token']);
        
        if (!$userId) {
            // Invalid token or session expired
            return array('status' => 'error', 'message' => 'Invalid user token');
        }
    
        // Check if the device belongs to the user
        if (!$this->isDeviceOwnedByUser($data['deviceId'], $userId)) {
            return array('status' => 'error', 'message' => 'Unauthorized access to the device');
        }
    
        // Prepare the SQL query to select device information
        $sql = "SELECT d.id, d.name FROM Devices d WHERE d.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $data["deviceId"]);
        $stmt->execute();
        $result = $stmt->get_result();
    
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

            // Fetch additional information from the DeviceScheduler for this device
            $sql = "SELECT * FROM DeviceScheduler WHERE DeviceId = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $device['id']);
            $stmt->execute();
            $scheduleResult = $stmt->get_result();

            // Fetch the schedule data for the device
            $scheduleData = [];
            while ($scheduleRow = $scheduleResult->fetch_assoc()) {
                $scheduleData[] = $scheduleRow;
            }
    
            // Return success response with device and queue information
            $stmt->close();
            return array(
                'status' => 'success',
                'device' => $device,
                'queue' => $queueData,
                'schedule' => $scheduleData
            );
        } else {
            // No device found
            $stmt->close();
            return array('status' => 'error', 'message' => 'No device found');
        }
    }
    
    public function addCommandToQueue($data) {
        // Get the user ID from the session token
        $userId = $this->getUserIdFromToken($data['token']);
        
        if (!$userId) {
            // Invalid token or session expired
            return array('status' => 'error', 'message' => 'Invalid user token');
        }
    
        // Check if the device belongs to the user
        if (!$this->isDeviceOwnedByUser($data['deviceId'], $userId)) {
            return array('status' => 'error', 'message' => 'Unauthorized access to the device');
        }
    
        // Prepare the SQL query to insert the command into DeviceQueue
        $sql = "INSERT INTO DeviceQueue (DeviceId, command, info, issuedOn) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $data["deviceId"], $data["command"], $data["info"]);
    
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
    }

    public function executeCommandFromQueue($deviceToken) {
        // Prepare the SQL query to select the device by its token
        $sql = "SELECT id FROM Devices WHERE token = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $deviceToken);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Check if a device with the provided token exists
        if ($result->num_rows > 0) {
            // Fetch the device ID
            $device = $result->fetch_assoc();
            $deviceId = $device['id'];
    
            // Prepare the SQL query to select the oldest unexecuted command for the device
            $sql = "SELECT * FROM DeviceQueue WHERE DeviceId = ? AND executedOn IS NULL ORDER BY issuedOn ASC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $deviceId);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Check if there is any unexecuted command
            if ($result->num_rows > 0) {
                // Fetch the oldest unexecuted command
                $command = $result->fetch_assoc();
    
                // Prepare the SQL query to update the command and mark it as executed
                $sql = "UPDATE DeviceQueue SET executedOn = NOW() WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
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

     // Create a new scheduled command for a device owned by the user
     public function createSchedule($data) {
        // First, verify the user using the session token
        $userId = $this->getUserIdFromToken($data['token']);
        if (!$userId) {
            return array('status' => 'error', 'message' => 'Invalid user token');
        }

        // Check if the device is owned by this user
        if (!$this->isDeviceOwnedByUser($data['deviceId'], $userId)) {
            return array('status' => 'error', 'message' => 'User does not own this device');
        }

        // Prepare the SQL query to insert the scheduled command into the DeviceScheduler table
        $sql = "INSERT INTO DeviceScheduler (DeviceId, command, info, time) VALUES (?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $this->conn->prepare($sql);

        // Bind the device ID, command, and time to the placeholders
        $stmt->bind_param("isss", $data['deviceId'], $data['command'], $data['info'], $data['time']);

        // Execute the query
        if ($stmt->execute()) {
            $stmt->close();
            return array('status' => 'success', 'message' => 'Schedule created successfully');
        } else {
            $stmt->close();
            return array('status' => 'error', 'message' => 'Error creating schedule');
        }
    }

    // Delete a scheduled command for a device owned by the user
    public function deleteSchedule($data) {
        // Verify the user using the session token
        $userId = $this->getUserIdFromToken($data['token']);
        if (!$userId) {
            return array('status' => 'error', 'message' => 'Invalid user token');
        }

        // Check if the device is owned by this user
        if (!$this->isDeviceOwnedByUser($data['deviceId'], $userId)) {
            return array('status' => 'error', 'message' => 'User does not own this device');
        }

        // Prepare the SQL query to delete the scheduled command
        $sql = "DELETE FROM DeviceScheduler WHERE id = ? AND DeviceId = ?";

        // Prepare the statement
        $stmt = $this->conn->prepare($sql);

        // Bind the schedule ID and device ID to the placeholders
        $stmt->bind_param("ii", $data['scheduleId'], $data['deviceId']);

        // Execute the query
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();
            return array('status' => 'success', 'message' => 'Schedule deleted successfully');
        } else {
            $stmt->close();
            return array('status' => 'error', 'message' => 'Error deleting schedule or schedule not found');
        }
    }

    // Execute the oldest unexecuted scheduled command for a device
    public function executeSchedule($deviceToken) {
    // First, get the device ID using the token
    $sql = "SELECT id FROM Devices WHERE token = ?";

    // Prepare the statement
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $deviceToken);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the device exists
    if ($result->num_rows > 0) {
        $device = $result->fetch_assoc();
        $deviceId = $device['id'];

        // Now get the oldest unexecuted command for the device from DeviceScheduler
        $sql = "SELECT * FROM DeviceScheduler WHERE DeviceId = ? ORDER BY lastExecuted ASC LIMIT 1";

        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $deviceId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a command exists
        if ($result->num_rows > 0) {
            $command = $result->fetch_assoc();

            // Perform the scheduled command execution logic here
            // For this example, we assume the execution is successful

            // Update the lastExecuted field after successful execution
            $sql = "UPDATE DeviceScheduler SET lastExecuted = NOW() WHERE id = ?";

            // Prepare the statement for updating
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $command['id']);
            $stmt->execute();

            // Return success response with the executed command details
            return array('status' => 'success', 'message' => 'Command executed successfully', 'command' => $command);
        } else {
            // No scheduled commands found
            return array('status' => 'error', 'message' => 'No scheduled commands found');
        }
    } else {
        // Invalid device token
        return array('status' => 'error', 'message' => 'Invalid device token');
    }
}


    // Helper function to get the user ID from a session token
    private function getUserIdFromToken($token) {
        $sql = "SELECT UserId FROM Sessions WHERE token = ? AND expireOn > NOW()";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['UserId'];
        } else {
            return false;
        }
    }

    // Helper function to check if the device is owned by the user
    private function isDeviceOwnedByUser($deviceId, $userId) {
        $sql = "SELECT id FROM Devices WHERE id = ? AND owner = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $deviceId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
    
}