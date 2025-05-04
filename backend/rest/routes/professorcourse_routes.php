<?php

require_once __DIR__ . '/../services/ProfessorCourseService.class.php';

Flight::set('professorcourse_service', new ProfessorCourseService());

Flight::group('/professor-courses', function(){
    /**
     * @OA\Get(
     *     path="/professor-courses/unassigned/{professor_id}",
     *     summary="Get unassigned courses for a professor",
     *     tags={"Professor Courses"},
     *     @OA\Parameter(
     *         name="professor_id",
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
    Flight::route('GET /unassigned/@professor_id', function($professor_id){
        $data = Flight::get('professorcourse_service')->get_professorcourse($professor_id);
        
        Flight::json($data, 200);
    });

    /**
     * @OA\Post(
     *     path="/professor-courses/add",
     *     summary="Assign a course to a professor",
     *     tags={"Professor Courses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ProfessorId", "CourseId"},
     *             @OA\Property(property="ProfessorId", type="integer"),
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

        $course = Flight::get('professorcourse_service')->add_professorcourse($payload);

        Flight::json(['message' => "You have successfully added the course for this professor", 'data' => $course]);
    });

    /**
     * @OA\Get(
     *     path="/professor-courses/assigned/{professor_id}",
     *     summary="Get assigned courses for a professor",
     *     tags={"Professor Courses"},
     *     @OA\Parameter(
     *         name="professor_id",
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
    Flight::route('GET /assigned/@professor_id', function($professor_id){
        $data = Flight::get('professorcourse_service')->get_professorcourse_ass($professor_id);
        
        Flight::json($data, 200);
    });

    /**
     * @OA\Delete(
     *     path="/professor-courses/delete/{professor_id}/{course_id}",
     *     summary="Remove a course from a professor",
     *     tags={"Professor Courses"},
     *     @OA\Parameter(
     *         name="professor_id",
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
    Flight::route('DELETE /delete/@professor_id/@course_id', function($professor_id, $course_id){
        Flight::get('professorcourse_service')->remove_professorcourse($professor_id, $course_id);
        Flight::json(['message'=> "You have successfully removed the course from the professor!"]);
    });
});
