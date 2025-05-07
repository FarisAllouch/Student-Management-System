<?php

require_once __DIR__ . '/../services/ExamService.class.php';

Flight::set('exam_service', new ExamService());

Flight::group('/exams', function() {
    /**
     * @OA\Get(
     *     path="/exams",
     *     summary="Get all exams",
     *     tags={"Exams"},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of exams"
     *     )
     * )
     */
    Flight::route('GET /', function() {
    
        $payload = Flight::request()->query;
    
        $data = Flight::get('exam_service')->get_exams($payload['CourseId']);
    
        foreach ($data as $i => $exam) {
            $data[$i]['action'] = '<div class="btn-group" role="group" aria-label="Actions">'
                                . '<button type="button" class="btn btn-warning" onclick="ExamService.open_edit_exam_modal('. $exam['ExamId'] . ')">Edit</button>'
                                . '<button type="button" class="btn btn-danger"  onclick="ExamService.delete_exam('. $exam['ExamId'] . ')">Delete</button>'
                                . '</div>';
        }
        //Response
        Flight::json([
            'draw'            => isset($payload['draw']) ? (int)$payload['draw'] : 1,
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data
        ], 200);
    });

    /**
     * @OA\Post(
     *     path="/exams",
     *     summary="Create a new exam",
     *     tags={"Exams"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"CourseId", "ExamName", "ExamDate"},
     *             @OA\Property(property="CourseId", type="integer"),
     *             @OA\Property(property="ExamName", type="string"),
     *             @OA\Property(property="ExamDate", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exam created successfully"
     *     )
     * )
     */
    Flight::route('POST /add', function(){
        $payload = Flight::request()->data->getData();

        if ($payload['ExamName'] == NULL || $payload['ExamName'] == '') {
            Flight::halt(500, "Exam name field is missing");
        }

        if ($payload['ExamId'] != NULL && $payload['ExamId'] != '') {
            $exam = Flight::get('exam_service')->edit_exam($payload);
        } else {
            unset($payload['ExamId']);
            $exam = Flight::get('exam_service')->add_exam($payload);
        }

        Flight::json(['message' => "You have successfully added the exam for this course", 'data' => $exam]);
    });

    /**
     * @OA\Delete(
     *     path="/exams/{id}",
     *     summary="Delete exam",
     *     tags={"Exams"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exam deleted successfully"
     *     )
     * )
     */
    Flight::route('DELETE /delete/@exam_id', function($exam_id){
        if ($exam_id == NULL || $exam_id == '') {
            Flight::halt(500, "You have to provide a exam id!");
        }
    
        Flight::get('exam_service')->delete_exam_by_id($exam_id);
        Flight::json(['message'=> "You have successfully deleted the exam!"]);
    });

    /**
     * @OA\Get(
     *     path="/exams/{id}",
     *     summary="Get exam by ID",
     *     tags={"Exams"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exam details"
     *     )
     * )
     */
    Flight::route('GET /@exam_id', function($exam_id) {
        $course = Flight::get('exam_service')->get_exam_by_id($exam_id);
    
        Flight::json($course, 200);
    });
});