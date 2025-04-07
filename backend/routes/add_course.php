<?php

require_once('../rest/services/CourseService.class.php');

$payload = $_REQUEST;

if ($payload == NULL || $payload == '') {
    header('HTTP/1.1 500 Bad Request');
    die(json_encode(['error'=> "Course field name is missing!"]));
}

$course_service = new CourseService();

if ($payload['id'] != NULL && $payload['id'] != '') {
    $course = $course_service->edit_course($payload);
} else {
    unset ($payload['id']);
    $course = $course_service->add_course($payload);
}


echo json_encode(['message' => "You have successfully added the course", 'data' => $course]);