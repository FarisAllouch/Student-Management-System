<?php

require_once __DIR__ . '/../services/CourseService.class.php';
require_once __DIR__ . '/../services/StudentService.class.php';

Flight::set('course_service', new CourseService());
Flight::set('student_service', new StudentService());

Flight::group('/courses', function() {

    Flight::route('GET /', function() {
    
        $payload = Flight::request()->query;
    
        $params = [
            'start' => (int)$payload['start'],
            'search' => $payload['search']['value'],
            'draw' => $payload['draw'],
            'limit' => (int)$payload['length'],
            'order_column' => $payload['order'][0]['name'],
            'order_direction' => $payload['order'][0]['dir']
        ];
    
        $data = Flight::get('course_service')->get_courses_paginated($params['start'], $params['limit'], $params['search'], $params['order_column'], $params['order_direction']);
    
        foreach ($data['data'] as $id => $course) {
            $data['data'][$id]['action'] = '<div class="btn-group" role="group" aria-label="Actions">' .
                                                '<button type="button" class="btn btn-warning" onclick="CourseService.open_edit_course_modal('. $course['id'] .')">Edit</button>' .
                                                '<button type="button" class="btn btn-danger" onclick="CourseService.delete_course('. $course['id'] .')">Delete</button>' .
                                                '<button type="button" class="btn btn-info" onclick="ExamService.manage_exams('.$course['id'] .')">Manage Exams</button>' .
                                                '<button type="button" class="btn btn-success" onclick="CourseService.get_enrolledStudents('.$course['id'] .')">Enrolled students</button>' .
                                            '</div>';
        }
    
        //Response
        Flight::json([
            'draw' => $params['draw'],
            'data' => $data['data'],
            'recordsFiltered' => $data['count'],
            'recordsTotal' => $data['count'],
            'end' => $data['count']
        ], 200);
    });
    
    Flight::route('POST /add', function() {
        $payload = Flight::request()->data->getData();
    
        if ($payload['CourseName'] == NULL || $payload['CourseName'] == '') {
            Flight::halt(500, "Course name field is missing");
        }
    
        if ($payload['id'] != NULL && $payload['id'] != '') {
            $course = Flight::get('course_service')->edit_course($payload);
        } else {
            unset($payload['id']);
            $course = Flight::get('course_service')->add_course($payload);
        }
    
        Flight::json(['message' => "You have successfully added the course", 'data' => $course]);
    });
    
   
    Flight::route('DELETE /delete/@course_id', function($course_id) {
        
        if ($course_id == NULL || $course_id == '') {
            Flight::halt(500, "You have to provide a course id!");
        }
    
        Flight::get('course_service')->delete_course_by_id($course_id);
        Flight::json(['message'=> "You have successfully deleted the course!"]);
    });

    
    Flight::route('GET /@course_id', function($course_id) {
        $course = Flight::get('course_service')->get_course_by_id($course_id);
        Flight::json($course, 200);
    });

    Flight::route('GET /@course_id/students', function($course_id) {
        $course_students = Flight::get('student_service')->get_course_students($course_id);
        Flight::json($course_students, 200);
    });
});