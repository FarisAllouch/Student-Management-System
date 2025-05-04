<?php

require_once __DIR__ . '/BaseDao.class.php';

class ProfessorCourseDao extends BaseDao {
    public function __construct() {
        parent::__construct('professorcourse');
    }

    public function get_professorcourse($professor_id) {
        $query = "SELECT * FROM course WHERE id NOT IN (
            SELECT CourseId FROM professorcourse WHERE ProfessorId = :professor_id)";
        return $this->query($query, ['professor_id' => $professor_id]);
    }

    public function get_professorcourse_ass($professor_id){
        $query = "SELECT c.* 
                    FROM course c 
                    JOIN professorcourse pc ON c.id = pc.CourseId 
                    WHERE pc.ProfessorId = :professor_id";
        return $this->query($query, ['professor_id' => $professor_id]);
    }

    public function add_professorcourse($professor_id, $course_id){
        $query = "INSERT INTO professorcourse (ProfessorId, CourseId) VALUES (:professor_id, :course_id)";
        $this->execute($query, [
            'professor_id' => $professor_id,
            'course_id' => $course_id
        ]);
    }

    public function remove_professorcourse($professor_id, $course_id){
        $query = "DELETE FROM professorcourse WHERE ProfessorId = :professor_id AND CourseId = :course_id";
        $this->execute($query, ['professor_id' => $professor_id, 'course_id' => $course_id]);
    }
}