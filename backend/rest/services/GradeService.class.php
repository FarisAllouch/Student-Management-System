<?php

require_once __DIR__ . '/../dao/GradeDao.class.php';

class GradeService {
    private $grade_dao;

    public function __construct(){
        $this->grade_dao = new GradeDao();
    }

    public function get_student_grades($student_id, $course_id) {
        return $this->grade_dao->get_student_grades($student_id, $course_id);
    }

    public function add_grade($grade) {
        return $this->grade_dao->add_grade($grade);
    }

    public function remove_grade($grade_id) {
        $this->grade_dao->remove_grade($grade_id);
        return ['message' => 'Grade removed successfully'];
    }
} 