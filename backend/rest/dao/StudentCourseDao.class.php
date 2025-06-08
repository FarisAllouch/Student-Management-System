<?php

require_once __DIR__ . '/BaseDao.class.php';

class StudentCourseDao extends BaseDao {
    public function __construct() {
        parent::__construct('studentcourse');
    }

    public function get_studentcourse($student_id) {
        $query = "SELECT * FROM course WHERE id NOT IN (
            SELECT CourseId FROM studentcourse WHERE StudentId = :student_id)";
        return $this->query($query, ['student_id' => $student_id]);
    }

    public function get_studentcourse_ass($student_id){
        $query = "SELECT c.* 
                    FROM course c 
                    JOIN studentcourse sc ON c.id = sc.CourseId 
                    WHERE sc.StudentId = :student_id";
        return $this->query($query, ['student_id' => $student_id]);
    }

    public function get_studentcourse_ass_prof($student_id, $professor_id){
        $query = "SELECT c.* 
                FROM course c 
                JOIN studentcourse sc ON c.id = sc.CourseId 
                JOIN professorcourse pc ON c.id = pc.CourseId 
                WHERE sc.StudentId = :student_id AND pc.ProfessorId = :professor_id";
                
        return $this->query($query, [
        'student_id' => $student_id,
        'professor_id' => $professor_id
        ]);
    }

    public function add_studentcourse($student_id, $course_id){
        $query = "INSERT INTO studentcourse (StudentId, CourseId) VALUES (:student_id, :course_id)";
        $this->execute($query, [
            'student_id' => $student_id,
            'course_id' => $course_id
        ]);
    }

    public function remove_studentcourse($student_id, $course_id){
        $query = "DELETE FROM studentcourse WHERE StudentId = :student_id AND CourseId = :course_id";
        $this->execute($query, ['student_id' => $student_id, 'course_id' => $course_id]);
    }
} 