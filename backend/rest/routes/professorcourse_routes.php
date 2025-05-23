<?php

require_once __DIR__ . '/../services/ProfessorCourseService.class.php';

Flight::set('professorcourse_service', new ProfessorCourseService());

Flight::group('/professor-courses', function(){

    Flight::route('GET /unassigned/@professor_id', function($professor_id){
        $data = Flight::get('professorcourse_service')->get_professorcourse($professor_id);
        
        Flight::json($data, 200);
    });

    Flight::route('POST /add', function(){
        $payload = Flight::request()->data->getData();

        $course = Flight::get('professorcourse_service')->add_professorcourse($payload);

        Flight::json(['message' => "You have successfully added the course for this professor", 'data' => $course]);
    });

    Flight::route('GET /assigned/@professor_id', function($professor_id){
        $data = Flight::get('professorcourse_service')->get_professorcourse_ass($professor_id);
        
        Flight::json($data, 200);
    });

    Flight::route('DELETE /delete/@professor_id/@course_id', function($professor_id, $course_id){
        Flight::get('professorcourse_service')->remove_professorcourse($professor_id, $course_id);
        Flight::json(['message'=> "You have successfully removed the course from the professor!"]);
    });
});
