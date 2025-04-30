<?php

require_once __DIR__ . '/BaseDao.class.php';

class ExamDao extends BaseDao {
    public function __construct() {
        parent::__construct('courseexams');
    }

    public function add_exam($exam) {
        return $this->insert('courseexams', $exam);
    }

    public function get_exams($course_id) {
        $query = "SELECT * FROM courseexams WHERE CourseId = :course_id";
        return $this->query($query, ['course_id' => $course_id]);
    }

    public function delete_exam_by_id($id) {
        $query = "DELETE FROM courseexams WHERE ExamId = :id";
        $this->execute($query, [
            'id' => $id
        ]);
    }

    public function get_exam_by_id($exam_id) {
        return $this->query_unique(
    "SELECT * FROM courseexams WHERE ExamId = :id", 
    [
                'id' => $exam_id
            ]
        );    
    }

    public function edit_exam($exam) {
        $query = "UPDATE courseexams SET ExamName = :ExamName, Weight = :Weight WHERE ExamId = :ExamId";
        return $this->execute($query, [
            'ExamName' => $exam['ExamName'],
            'Weight' => $exam['Weight'],
            'ExamId' => $exam['ExamId']
        ]);
    }
}