<?php

require_once __DIR__ . '/../services/StudentCourseService.class.php';

Flight::set('studentcourse_service', new StudentCourseService());

Flight::group('/student-courses', function(){

    Flight::route('GET /unassigned/@student_id', function($student_id){
        authorizeRole('admin');
        $data = Flight::get('studentcourse_service')->get_studentcourse($student_id);
        
        Flight::json($data, 200);
    });

    Flight::route('POST /add', function(){
        authorizeRole('admin');
        $payload = Flight::request()->data->getData();

        $result = Flight::get('studentcourse_service')->add_studentcourse($payload);

        if (isset($result['error'])) {
            Flight::json(['error' => $result['error']], 400);
        } else {
            Flight::json(['message' => "You have successfully added the course for this student", 'data' => $result]);
        }
    });

    Flight::route('GET /assigned/@student_id', function($student_id){
        authorizeRoles(['admin', 'professor']);
        $data = Flight::get('studentcourse_service')->get_studentcourse_ass($student_id);
        
        Flight::json($data, 200);
    });

    Flight::route('DELETE /delete/@student_id/@course_id', function($student_id, $course_id){
        authorizeRole('admin');
        Flight::get('studentcourse_service')->remove_studentcourse($student_id, $course_id);
        Flight::json(['message'=> "You have successfully removed the course from the student!"]);
    });
}); 