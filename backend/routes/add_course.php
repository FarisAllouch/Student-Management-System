<?php

require_once('../rest/services/CourseService.class.php');

$payload = json_decode(file_get_contents("php://input"), true);

if (!isset($payload['CourseName']) || trim($payload['CourseName']) === '') {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Course Name field is missing']);
    exit;
}

$course_service = new CourseService();

error_log("Payload ID: " . var_export($payload['id'], true));

if (isset($payload['id']) && is_numeric($payload['id'])) {
    $course = $course_service->edit_course($payload);
    echo json_encode(['message' => "You have successfully edited the course", 'data' => $course]);
} else {
    unset($payload['id']);
    $course = $course_service->add_course($payload);
    echo json_encode(['message' => "You have successfully added the course", 'data' => $course]);
}