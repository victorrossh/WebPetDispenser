<?php
require_once '../models/Device.php';

class DeviceController {
    public function create($userToken, $deviceName) {
        $device = new Device();
		$response = $device->create($userToken, $deviceName);
        return $response;
    }

    public function getDevice($deviceToken) {
        $device = new Device();
		$response = $device->getDevice($deviceToken);
        return $response;
    }

    public function addCommandToQueue($userToken, $deviceId, $command, $info) {
        $device = new Device();
		$response = $device->addCommandToQueue($userToken, $deviceId, $command, $info);
        return $response;
    }

    public function executeCommandFromQueue($deviceToken) {
        $device = new Device();
		$response = $device->executeCommandFromQueue($deviceToken);
        return $response;
    }

    public function createSchedule($userToken, $deviceId, $command, $info, $time) {
        $device = new Device();
		$response = $device->createSchedule($userToken, $deviceId, $command, $info, $time);
        return $response;
    }

    public function deleteSchedule($userToken, $deviceId, $scheduleId) {
        $device = new Device();
		$response = $device->deleteSchedule($userToken, $deviceId, $scheduleId);
        return $response;
    }

    public function executeSchedule($deviceToken, $deviceId) {
        $device = new Device();
		$response = $device->executeSchedule($deviceToken, $deviceId);
        return $response;
    }
}