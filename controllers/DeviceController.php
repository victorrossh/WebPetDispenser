<?php
require_once '../models/Device.php';

class DeviceController {
    public function create($data) {
        $device = new Device();
		$response = $device->create($data);
        return $response;
    }

    public function getdata($data) {
        $device = new Device();
		$response = $device->getdata($data);
        return $response;
    }

    public function pushcommand($data) {
        $device = new Device();
		$response = $device->addCommandToQueue($data);
        return $response;
    }
    

    public function executecommand($data) {
        $device = new Device();
		$response = $device->executeCommandFromQueue($data);
        return $response;
    }

    public function createschedule($data) {
        $device = new Device();
		$response = $device->createSchedule($data);
        return $response;
    }

    public function deleteschedule($data) {
        $device = new Device();
		$response = $device->deleteSchedule($data);
        return $response;
    }

    public function executeschedule($data) {
        $device = new Device();
		$response = $device->executeSchedule($data);
        return $response;
    }
}