<?php

require_once('../rest/services/CourseService.class.php');

$payload = $_REQUEST;

$params = [
    'start' => (int)$payload['start'],
    'search' => $payload['search']['value'],
    'draw' => $payload['draw'],
    'limit' => (int)$payload['length'],
    'order_column' => $payload['order'][0]['name'],
    'order_direction' => $payload['order'][0]['dir']
];

$course_service = new CourseService();
//Get data query
$data = $course_service->get_courses_paginated($params['start'], $params['limit'], $params['search'], $params['order_column'], $params['order_direction']);

foreach ($data['data'] as $id => $course) {
    $data['data'][$id]['action'] = '<div class="btn-group" role="group" aria-label="Actions">' .
                                        '<button type="button" class="btn btn-warning" onclick="CourseService.open_edit_course_modal('. $course['id'] .')">Edit</button>' .
                                        '<button type="button" class="btn btn-danger" onclick="CourseService.delete_course('. $course['id'] .')">Delete</button>' .
                                    '</div>';
}

//Response
echo json_encode([
    'draw' => $params['draw'],
    'data' => $data['data'],
    'recordsFiltered' => $data['count'],
    'recordsTotal' => $data['count'],
    'end' => $data['count']
]);