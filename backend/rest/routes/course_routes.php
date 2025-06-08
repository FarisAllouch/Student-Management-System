<?php

require_once __DIR__ . '/../services/CourseService.class.php';
require_once __DIR__ . '/../services/StudentService.class.php';
require_once __DIR__ . '/../../helpers/auth_helpers.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @OA\Tag(
 *     name="Courses",
 *     description="Course management endpoints"
 * )
 */

Flight::set('course_service', new CourseService());
Flight::set('student_service', new StudentService());

Flight::group('/courses', function() {

    /**
    * @OA\Get(
    *      path="/courses/all",
    *      tags={"courses"},
    *      summary="Get all courses",
    *      @OA\Response(
    *           response=200,
    *           description="Array of all courses in the database"
    *      )
    * )
    */
    Flight::route('GET /all', function() {
        authorizeRole('admin');
        $data = Flight::get('course_service')->get_all_courses();
        Flight::json($data, 200);
    });

    Flight::route('GET /', function() {
        authorizeRoles(['professor', 'student', 'admin']);
        $user = Flight::get('user');

        $payload = Flight::request()->query;

        $params = [
            'start' => (int)$payload['start'],
            'search' => $payload['search']['value'],
            'draw' => $payload['draw'],
            'limit' => (int)$payload['length'],
            'order_column' => $payload['order'][0]['name'],
            'order_direction' => $payload['order'][0]['dir']
        ];

        if ($user->role === 'student') {
            $params['studentId'] = $user->id;
            $data = Flight::get('course_service')->get_courses_paginated_student($params['start'], $params['limit'], $params['search'], $params['order_column'], $params['order_direction'], $params['studentId']);
        } elseif ($user->role === 'professor') {
            $params['professorId'] = $user->id;
            $data = Flight::get('course_service')->get_courses_paginated_professor($params['start'], $params['limit'], $params['search'], $params['order_column'], $params['order_direction'], $params['professorId']);
        } else {
            $data = Flight::get('course_service')->get_courses_paginated($params['start'], $params['limit'], $params['search'], $params['order_column'], $params['order_direction']);
        }

        foreach ($data['data'] as $id => $course) {
            $buttons = '<div class="btn-group" role="group" aria-label="Actions">';

            if ($user->role === 'admin') {
                $buttons .= '<button type="button" class="btn btn-warning" onclick="CourseService.open_edit_course_modal('. $course['id'] .')">Edit</button>';
                $buttons .= '<button type="button" class="btn btn-danger" onclick="CourseService.delete_course('. $course['id'] .')">Delete</button>';
            }
            if ($user->role === 'admin' || $user->role === 'professor') {
                $buttons .= '<button type="button" class="btn btn-info" onclick="ExamService.manage_exams('.$course['id'] .')">Manage Exams</button>';
                $buttons .= '</div>';
            }

            if ($user->role === 'student') {
                $buttons .= '<button type="button" class="btn btn-info" onclick="StudentService.load_student_grades(' . $user->id . ', ' . $course['id'] . ')">See Grades</button>';
                $buttons .= '</div>';
            }

            $data['data'][$id]['action'] = $buttons;
        }

        Flight::json([
            'draw' => $params['draw'],
            'data' => $data['data'],
            'recordsFiltered' => $data['count'],
            'recordsTotal' => $data['count'],
            'end' => $data['count']
        ], 200);
    });
    
    /**
     * @OA\Post(
     *      path="/courses/add",
     *      tags={"courses"},
     *      summary="Add course data to the database",
     *      @OA\RequestBody(
     *          description="Course data payload",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"CourseName", "CourseCode"},
     *              @OA\Property(property="id", type="string", example="", description="Course Id"),
     *              @OA\Property(property="CourseName", type="string", example="Calculus 1", description="Course Name"),
     *              @OA\Property(property="CourseCode", type="string", example="IT-201", description="Course Code")
     *          )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="Course data, or exception if course is not added properly"
     *      )
     * )
     */
    Flight::route('POST /add', function() {
        authorizeRole('admin');
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
    
    /**
     * @OA\DELETE(
     *      path="/courses/delete/{id}",
     *      tags={"courses"},
     *      summary="Delete course by id",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the course",
     *          @OA\Schema(type="number", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Deleted course data or 500 status code exception otherwise"
     *      )
     * )
     */
    Flight::route('DELETE /delete/@course_id', function($course_id) {
        authorizeRole('admin');
        if ($course_id == NULL || $course_id == '') {
            Flight::halt(500, "You have to provide a course id!");
        }
    
        Flight::get('course_service')->delete_course_by_id($course_id);
        Flight::json(['message'=> "You have successfully deleted the course!"]);
    });

    /**
     * @OA\GET(
     *      path="/courses/{id}",
     *      tags={"courses"},
     *      summary="Get course by id",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the course",
     *          @OA\Schema(type="number", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Course data, or false if course does not exists"
     *      )
     * )
     */
    Flight::route('GET /@course_id', function($course_id) {
        authorizeRole('admin');
        $course = Flight::get('course_service')->get_course_by_id($course_id);
        Flight::json($course, 200);
    });
    
    /**
     * @OA\GET(
     *      path="/courses/{id}/students",
     *      tags={"courses"},
     *      summary="Get students of course by course id",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the course",
     *          @OA\Schema(type="number", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Students of course data, or false if course does not exists"
     *      )
     * )
     */
    Flight::route('GET /@course_id/students', function($course_id) {
        authorizeRoles(['admin', 'professor']);
        $course_students = Flight::get('student_service')->get_course_students($course_id);
        Flight::json($course_students, 200);
    });
});