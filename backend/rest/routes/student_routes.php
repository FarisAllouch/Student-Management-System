<?php
require_once __DIR__ . '/../services/StudentService.class.php';

Flight::set('student_service', new StudentService());

Flight::group('/students', function(){

    Flight::route('POST /add', function() {
        authorizeRole('admin');
        $payload = Flight::request()->data->getData();
    
        if ($payload['FirstName'] == NULL || $payload['FirstName'] == '') {
            Flight::halt(500, "First name field is missing");
        }
    
        if ($payload['id'] != NULL && $payload['id'] != '') {
            $student = Flight::get('student_service')->edit_student($payload);
        } else {
            unset($payload['id']);
            $student = Flight::get('student_service')->add_student($payload);
        }
    
        Flight::json(['message' => "You have successfully added the student", 'data' => $student]);
    });

    Flight::route('GET /', function() {
        authorizeRoles(['admin', 'professor']);

        $payload = Flight::request()->query;

        $params = [
            'start' => (int)$payload['start'],
            'search' => $payload['search']['value'],
            'draw' => $payload['draw'],
            'limit' => (int)$payload['length'],
            'order_column' => $payload['order'][0]['name'],
            'order_direction' => $payload['order'][0]['dir']
        ];

        $data = Flight::get('student_service')->get_students_paginated($params['start'], $params['limit'], $params['search'], $params['order_column'], $params['order_direction']);

        foreach ($data['data'] as $id => $student) {
            $data['data'][$id]['action'] = '<div class="btn-group" role="group" aria-label="Actions">' .
                                                '<button type="button" class="btn btn-warning" onclick="StudentService.open_edit_student_modal('. $student['id'] .')">Edit</button>' .
                                                '<button type="button" class="btn btn-danger" onclick="StudentService.delete_student('. $student['id'] .')">Delete</button>' .
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
    
    Flight::route('DELETE /delete/@student_id', function($student_id) {
        authorizeRole('admin');
        
        if ($student_id == NULL || $student_id == '') {
            Flight::halt(500, "You have to provide a student id!");
        }
    
        Flight::get('student_service')->delete_student_by_id($student_id);
        Flight::json(['message'=> "You have successfully deleted the student!"]);
    });
    
    Flight::route('GET /@student_id', function($student_id) {
        authorizeRole('admin');
        $student = Flight::get('student_service')->get_student_by_id($student_id);
    
        Flight::json($student, 200);
    });

    /**
     * @OA\GET(
     *      path="/students/details",
     *      tags={"students"},
     *      summary="Get logged in professor information",
     *      security={
     *          {"ApiKey": {}}
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="Student details"
     *      )
     * )
     */
    Flight::route('GET /details', function() {
        authorizeRole('admin');
        Flight::json(Flight::get('user'));
    });
});