<?php

require_once __DIR__ . '/../services/StudentCourseService.class.php';

Flight::set('studentcourse_service', new StudentCourseService());

Flight::group('/student-courses', function(){
    /**
     * @OA\Get(
     *     path="/student-courses/unassigned/{student_id}",
     *     summary="Get unassigned courses for a student",
     *     tags={"Student Courses"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of unassigned courses"
     *     )
     * )
     */
    Flight::route('GET /unassigned/@student_id', function($student_id){
        $data = Flight::get('studentcourse_service')->get_studentcourse($student_id);
        
        Flight::json($data, 200);
    });

    /**
     * @OA\Post(
     *     path="/student-courses/add",
     *     summary="Assign a course to a student",
     *     tags={"Student Courses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"StudentId", "CourseId"},
     *             @OA\Property(property="StudentId", type="integer"),
     *             @OA\Property(property="CourseId", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course assigned successfully"
     *     )
     * )
     */
    Flight::route('POST /add', function(){
        $payload = Flight::request()->data->getData();

        $result = Flight::get('studentcourse_service')->add_studentcourse($payload);

        if (isset($result['error'])) {
            Flight::json(['error' => $result['error']], 400);
        } else {
            Flight::json(['message' => "You have successfully added the course for this student", 'data' => $result]);
        }
    });

    /**
     * @OA\Get(
     *     path="/student-courses/assigned/{student_id}",
     *     summary="Get assigned courses for a student",
     *     tags={"Student Courses"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of assigned courses"
     *     )
     * )
     */
    Flight::route('GET /assigned/@student_id', function($student_id){
        $data = Flight::get('studentcourse_service')->get_studentcourse_ass($student_id);
        
        Flight::json($data, 200);
    });

    /**
     * @OA\Delete(
     *     path="/student-courses/delete/{student_id}/{course_id}",
     *     summary="Remove a course from a student",
     *     tags={"Student Courses"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course removed successfully"
     *     )
     * )
     */
    Flight::route('DELETE /delete/@student_id/@course_id', function($student_id, $course_id){
        Flight::get('studentcourse_service')->remove_studentcourse($student_id, $course_id);
        Flight::json(['message'=> "You have successfully removed the course from the student!"]);
    });
}); 