# Device Management API

This API allows you to manage devices, authenticate users, and issue commands to devices. It includes endpoints for user registration, login, device registration, and queueing/executing commands for devices.

## Table of Contents

1. [Authentication](#authentication)
   - [Register User](#register-user)
   - [Login User](#login-user)
2. [Device Management](#device-management)
   - [Register Device](#register-device)
   - [Get Device Info](#get-device-info)
3. [Command Management](#command-management)
   - [Push Command to Device](#push-command-to-device)
   - [Execute Oldest Command](#execute-oldest-command)
4. [User Info](#user-info)
   - [Get User Info](#get-user-info)

---

## Authentication

### Register User

**Endpoint**: `/api/register`

**Method**: `POST`

**Description**: Registers a new user.

**Request Body**:
```json
{
  "name": "User Name",
  "email": "user@example.com",
  "password": "password"
}
```

**Response**:
- On success: `201 Created`
```json
{
  "status": "success",
  "message": "User registered successfully"
}
```
- On failure: `400 Bad Request`
```json
{
  "status": "error",
  "message": "User already exists"
}
```

### Login User

**Endpoint**: `/api/login`

**Method**: `POST`

**Description**: Authenticates a user and returns a session token.

**Request Body**:
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response**:
- On success:
```json
{
  "status": "success",
  "token": "user_token"
}
```
- On failure: `401 Unauthorized`
```json
{
  "status": "error",
  "message": "Invalid credentials"
}
```

---

## Device Management

### Register Device

**Endpoint**: `/api/registerdevice`

**Method**: `POST`

**Description**: Registers a new device associated with the authenticated user.

**Request Body**:
```json
{
  "token": "user_token",
  "name": "Device Name"
}
```

**Response**:
- On success:
```json
{
  "status": "success",
  "message": "Device created successfully",
  "deviceToken": "device_token"
}
```
- On failure: `400 Bad Request`
```json
{
  "status": "error",
  "message": "Failed to create device"
}
```

### Get Device Info

**Endpoint**: `/api/device`

**Method**: `GET`

**Description**: Retrieves information about a device using its token.

**Query Parameters**:
```url
/api/device?token=device_token
```

**Response**:
- On success:
```json
{
  "status": "success",
  "device": {
    "id": 1,
    "name": "Device Name",
    "owner": 1
  }
}
```
- On failure: `404 Not Found`
```json
{
  "status": "error",
  "message": "Device not found"
}
```

---

## Command Management

### Push Command to Device

**Endpoint**: `/api/command`

**Method**: `POST`

**Description**: Adds a command to the device's command queue.

**Request Body**:
```json
{
  "token": "user_token",
  "deviceId": 1,
  "command": "feed",
  "info": "200g"
}
```

**Response**:
- On success:
```json
{
  "status": "success",
  "message": "Command added to queue"
}
```
- On failure: `400 Bad Request`
```json
{
  "status": "error",
  "message": "Failed to add command"
}
```

### Execute Oldest Command

**Endpoint**: `/api/command`

**Method**: `GET`

**Description**: Executes the oldest command in the device's queue.

**Query Parameters**:
```url
/api/execute_command?token=device_token
```

**Response**:
- On success:
```json
{
  "status": "success",
  "command": {
    "id": 1,
    "command": "feed",
    "issuedOn": "2024-10-24T12:00:00",
    "executedOn": "2024-10-24T12:30:00"
  }
}
```
- On failure: `404 Not Found`
```json
{
  "status": "error",
  "message": "No unexecuted commands found"
}
```

---

## User Info

### Get User Info

**Endpoint**: `/api/user`

**Method**: `GET`

**Description**: Retrieves information about the authenticated user using their session token.

**Query Parameters**:
```url
/api/get_user_info?token=user_token
```

**Response**:
- On success:
```json
{
  "status": "success",
  "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "admin": false
  }
}
```
- On failure: `404 Not Found`
```json
{
  "status": "error",
  "message": "User not found"
}
```

### Scheduling Management

#### Create Schedule

**POST** `/api/schedule`

This endpoint allows you to schedule a command for a device at a specific time.

```bash
POST http://localhost:8000/api/schedule
Content-Type: application/json

{
  "token": "user_token_example",
  "deviceId": 1,
  "command": "feed",
  "info": "200g",
  "time": "15:00:00"
}
```

**Request Fields**:
- `token`: The user’s session token.
- `deviceId`: The ID of the device for which the schedule is created.
- `command`: The command to schedule.
- `time`: The time at which the command should be executed.

**Response**:
```json
{
  "status": "success",
  "message": "Schedule created successfully"
}
```

#### Delete Schedule

**DELETE** `/api/schedule/delete`

This endpoint allows you to delete a scheduled command for a device.

```bash
DELETE http://localhost:8000/api/schedule
Content-Type: application/json

{
  "token": "user_token_example",
  "deviceId": 1,
  "scheduleId": 10
}
```

**Request Fields**:
- `token`: The user’s session token.
- `deviceId`: The ID of the device for which the schedule is being deleted.
- `scheduleId`: The ID of the scheduled command to delete.

**Response**:
```json
{
  "status": "success",
  "message": "Schedule deleted successfully"
}
```

#### Execute Schedule

**GET** `/api/schedule/execute?token=device_token_example`

This endpoint allows you to execute the oldest unexecuted scheduled command for a device.

```bash
GET http://localhost:8000/api/schedule?token=device_token_example
```

**Request Parameters**:
- `token`: The device’s token to identify the device for which the scheduled command should be executed.

**Response**:
```json
{
  "status": "success",
  "message": "Command executed successfully",
  "command": {
    "id": 1,
    "DeviceId": 1,
    "command": "feed",
    "time": "15:00:00",
    "lastExecuted": "2024-10-24 12:00:00"
  }
}
```