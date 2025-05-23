<?php

require_once __DIR__ . '/BaseDao.class.php';

class GradeDao extends BaseDao {
    public function __construct() {
        parent::__construct('coursegrade');
    }

    public function get_student_grades($student_id, $course_id) {
        $query = "SELECT cg.*, ce.ExamName, ce.Weight 
                 FROM coursegrade cg 
                 JOIN courseexams ce ON cg.ExamId = ce.ExamId
                 JOIN studentcourse sc ON cg.StudentCourseId = sc.id
                 WHERE sc.studentId = :student_id 
                 AND sc.courseId = :course_id";
        
        return $this->query($query, [
            'student_id' => $student_id, 
            'course_id' => $course_id
        ]);
    }

    public function add_grade($grade) {
        $query = "SELECT id FROM studentcourse WHERE studentId = :student_id AND courseId = :course_id LIMIT 1";  
        $result = $this->query_unique($query, [
            'student_id' => $grade['StudentId'],
            'course_id' => $grade['CourseId'],
        ]);

        if(empty($result)){
            return false;
        }

        $student_course_id = $result['id'];

        $gradeexam = [
            'ExamId' => $grade['ExamId'],
            'StudentCourseId' => $student_course_id,
            'Grade' => $grade['Grade']
        ];

        return $this->insert('coursegrade', $gradeexam);
    }

    public function remove_grade($grade_id) {
        $query = "DELETE FROM coursegrade WHERE id = :id";
        return $this->execute($query, ['id' => $grade_id]);
    }
} 