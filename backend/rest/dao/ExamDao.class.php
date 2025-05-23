<?php

require_once __DIR__ . '/BaseDao.class.php';

class ExamDao extends BaseDao {
    public function __construct() {
        parent::__construct('courseexams');
    }

    public function check_total_weight($course_id) {
        $query = "SELECT SUM(Weight) AS totalWeight FROM courseexams WHERE CourseId = :course_id";
        $result = $this->query_unique($query, ['course_id' => $course_id]);
    
        return $result['totalWeight'] ?? 0;
    }
    
    public function add_exam($exam) {
        $totalWeight = $this->check_total_weight($exam['CourseId']);
    
        if (($totalWeight + $exam['Weight']) > 100) {
            throw new Exception("Total weight of exams for this course cannot exceed 100%");
        }
    
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

public function get_exams_without_grades($student_id, $course_id) {
    $query = "SELECT ce.* 
              FROM courseexams ce
              WHERE ce.CourseId = :course_id
              AND ce.ExamId NOT IN (
                  SELECT cg.ExamId
                  FROM coursegrade cg
                  JOIN studentcourse sc ON cg.StudentCourseId = sc.id
                  WHERE sc.StudentId = :student_id AND sc.courseId = :course_id
              )";

    return $this->query($query, [
        'student_id' => $student_id,
        'course_id' => $course_id
    ]);
}
}