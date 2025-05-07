<?php
/**
 * @OA\Info(
 *     title="Student Management System API",
 *     version="1.0.0",
 *     description="API for managing students, courses, and professors"
 * )
 */

require_once __DIR__ . '/../services/StudentService.class.php';

Flight::set('student_service', new StudentService());

Flight::group('/students', function(){

    /**
     * @OA\Post(
     *     path="/students",
     *     summary="Create a new student",
     *     tags={"Students"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"FirstName", "LastName", "Email", "Password"},
     *             @OA\Property(property="FirstName", type="string"),
     *             @OA\Property(property="LastName", type="string"),
     *             @OA\Property(property="Email", type="string"),
     *             @OA\Property(property="Password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student created successfully"
     *     )
     * )
     */
    Flight::route('POST /add', function() {
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

    /**
     * @OA\Get(
     *     path="/students",
     *     summary="Get all students",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="start",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="length",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of students"
     *     )
     * )
     */
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
    
    /**
     * @OA\Delete(
     *     path="/students/{id}",
     *     summary="Delete student",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student deleted successfully"
     *     )
     * )
     */
    Flight::route('DELETE /delete/@student_id', function($student_id) {
        
        if ($student_id == NULL || $student_id == '') {
            Flight::halt(500, "You have to provide a student id!");
        }
    
        Flight::get('student_service')->delete_student_by_id($student_id);
        Flight::json(['message'=> "You have successfully deleted the student!"]);
    });
    
    /**
     * @OA\Get(
     *     path="/students/{id}",
     *     summary="Get student by ID",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student details"
     *     )
     * )
     */
    Flight::route('GET /@student_id', function($student_id) {
        $student = Flight::get('student_service')->get_student_by_id($student_id);
    
        Flight::json($student, 200);
    });
});