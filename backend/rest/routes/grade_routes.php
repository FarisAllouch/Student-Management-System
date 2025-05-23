<?php

require_once __DIR__ . '/../services/GradeService.class.php';

Flight::set('grade_service', new GradeService());

Flight::group('/grades', function(){
    Flight::route('GET /@student_id/@course_id', function($student_id, $course_id) {
        error_log("Received student_id: $student_id and course_id: $course_id");
    
        try {
            $data = Flight::get('grade_service')->get_student_grades($student_id, $course_id);
            Flight::json($data, 200);
        } catch (Exception $e) {
            Flight::error($e);
        }
    });

    Flight::route('POST /add', function(){
        try {
            $payload = Flight::request()->data->getData();
            $grade = Flight::get('grade_service')->add_grade($payload);
            Flight::json(['message' => "Grade added successfully", 'data' => $grade]);
        } catch (Exception $e) {
            Flight::error($e);
        }
    });

    Flight::route('DELETE /delete/@grade_id', function($grade_id){
        try {
            Flight::get('grade_service')->remove_grade($grade_id);
            Flight::json(['message' => "Grade removed successfully"]);
        } catch (Exception $e) {
            Flight::error($e);
        }
    });
}); 