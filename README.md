# WebPetDispenser API Documentation

### Overview
This API provides endpoints for managing users, devices, commands, and schedules in the WebPetDispenser system. Each endpoint returns JSON responses, and the `token` parameter is used to authenticate most requests.

---

### Endpoints

---

#### 1. User Registration

- **URL**: `/api/register.php`
- **Method**: `POST`
- **Description**: Register a new user.
- **Request Body**:
  - `name` (string): User's name.
  - `email` (string): User's email.
  - `password` (string): User's password.
- **Response**:
  - **Success**:
    ```json
    {
      "name": "User's Name",
      "token": "authentication_token",
      "admin": false
    }
    ```
  - **Error - User Already Exists**:
    ```json
    {
      "status": "error",
      "message": "User already exists"
    }
    ```
  - **Error - Creation Error**:
    ```json
    {
      "status": "error",
      "message": "Error creating user"
    }
    ```

---

#### 2. User Login

- **URL**: `/api/login.php`
- **Method**: `POST`
- **Description**: Authenticate a user and generate an access token.
- **Request Body**:
  - `email` (string): User's email.
  - `password` (string): User's password.
- **Response**:
  - **Success**:
    ```json
    {
      "name": "User's Name",
      "token": "authentication_token",
      "admin": false
    }
    ```
  - **Error - Invalid Password**:
    ```json
    {
      "status": "error",
      "message": "Invalid password"
    }
    ```
  - **Error - User Not Found**:
    ```json
    {
      "status": "error",
      "message": "User not found"
    }
    ```

---

#### 3. Verify Login (Token Validation)

- **URL**: `/api/login.php`
- **Method**: `GET`
- **Description**: Check if a user's session token is valid.
- **Query Parameter**:
  - `token` (string): User's authentication token.
- **Response**:
  - **Success**:
    ```json
    {
      "status": "success",
      "user": {
        "id": "user_id",
        "name": "User's Name",
        "email": "user@example.com",
        "admin": false
      }
    }
    ```
  - **Error - Invalid Token**:
    ```json
    {
      "status": "error",
      "message": "Invalid token"
    }
    ```

---

#### 4. User Logout

- **URL**: `/api/logout.php`
- **Method**: `POST`
- **Description**: Log out the user and invalidate the session token.
- **Request Body**:
  - `token` (string): User's authentication token.
- **Response**:
  - **Success**:
    ```json
    {
      "status": "success",
      "message": "Logged out successfully"
    }
    ```
  - **Error - Invalid Token or Session Not Found**:
    ```json
    {
      "status": "error",
      "message": "Invalid token or session not found"
    }
    ```
  - **Error - Logout Failed**:
    ```json
    {
      "status": "error",
      "message": "Logout failed"
    }
    ```

---

#### 5. Get User Information

- **URL**: `/api/user.php`
- **Method**: `GET`
- **Description**: Retrieve information about the authenticated user and their devices.
- **Query Parameter**:
  - `token` (string): User's authentication token.
- **Response**:
  - **Success**:
    ```json
    {
      "status": "success",
      "user": {
        "id": "user_id",
        "name": "User's Name",
        "email": "user@example.com",
        "admin": false
      },
      "devices": [
        {
          "id": "device_id",
          "name": "Device Name",
          "token": "device_token"
        },
        ...
      ]
    }
    ```
  - **Error - Invalid Token**:
    ```json
    {
      "status": "error",
      "message": "Invalid Token"
    }
    ```
  - **Error - No Devices Found**:
    ```json
    {
      "status": "error",
      "message": "No devices found with the provided user"
    }
    ```

---

#### 6. Device Management

- **Create Device**
  - **URL**: `/api/device.php`
  - **Method**: `POST`
  - **Description**: Create a new device for the user.
  - **Request Body**:
    - `token` (string): User's authentication token.
    - `name` (string): Device name.
  - **Response**:
    - **Success**:
      ```json
      {
        "status": "success",
        "message": "Device created successfully",
        "deviceToken": "new_device_token"
      }
      ```
    - **Error - Invalid User Token**:
      ```json
      {
        "status": "error",
        "message": "Invalid user token"
      }
      ```

- **Delete Device**
  - **URL**: `/api/device.php`
  - **Method**: `DELETE`
  - **Description**: Delete a device owned by the user.
  - **Request Body**:
    - `token` (string): User's authentication token.
    - `deviceId` (int): Device ID.
  - **Response**:
    - **Success**:
      ```json
      {
        "status": "success",
        "message": "Device deleted successfully"
      }
      ```
    - **Error - Unauthorized**:
      ```json
      {
        "status": "error",
        "message": "User does not own this device"
      }
      ```
    - **Error - Device Not Found**:
      ```json
      {
        "status": "error",
        "message": "Error deleting device or device not found"
      }
      ```

---

#### 7. Command Queue Management

- **Add Command to Queue**
  - **URL**: `/api/command.php`
  - **Method**: `POST`
  - **Request Body**:
    - `token` (string): User's authentication token.
    - `deviceId` (int): Device ID.
    - `command` (string): Command to be added to the queue.
    - `info` (string, optional): Additional information about the command.
  - **Response**:
    - **Success**:
      ```json
      {
        "status": "success",
        "message": "Command added to queue"
      }
      ```
    - **Error - Unauthorized**:
      ```json
      {
        "status": "error",
        "message": "Unauthorized access to the device"
      }
      ```

---

#### 8. Scheduling Commands

- **Create Schedule**
  - **URL**: `/api/schedule.php`
  - **Method**: `POST`
  - **Request Body**:
    - `token` (string): User's authentication token.
    - `deviceId` (int): Device ID.
    - `command` (string): Command to be scheduled.
    - `info` (string): Info about the command.
    - `time` (string): Scheduled time in `YYYY-MM-DD HH:MM:SS` format.
  - **Response**:
    - **Success**:
      ```json
      {
        "status": "success",
        "message": "Schedule created successfully"
      }
      ```
    - **Error - Unauthorized**:
      ```json
      {
        "status": "error",
        "message": "User does not own this device"
      }
      ```

- **Delete Schedule**
  - **URL**: `/api/schedule.php`
  - **Method**: `DELETE`
  - **Request Body**:
    - `token` (string): User's authentication token.
    - `deviceId` (int): Device ID.
    - `scheduleId` (int): Schedule ID.
  - **Response**:
    - **Success**:
      ```json
      {
        "status": "success",
        "message": "Schedule deleted successfully"
      }
      ```
    - **Error - Not Found**:
      ```json
      {
        "status": "error",
        "message": "Error deleting schedule or schedule not found"
      }
      ```