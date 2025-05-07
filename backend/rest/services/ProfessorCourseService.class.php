<?php

require_once __DIR__ . '/../dao/ProfessorCourseDao.class.php';

class ProfessorCourseService {
    private $professorcourse_dao;

    public function __construct(){
        $this->professorcourse_dao = new ProfessorCourseDao();
    }

    public function get_professorcourse($professor_id) {
        return $this->professorcourse_dao->get_professorcourse($professor_id);
    }

    public function get_professorcourse_ass($professor_id) {
        return $this->professorcourse_dao->get_professorcourse_ass($professor_id);
    }

    public function add_professorcourse($course){
        return $this->professorcourse_dao->add_professorcourse($course['ProfessorId'], $course['CourseId']);
    }

    public function remove_professorcourse($professor_id, $course_id) {
        $this->professorcourse_dao->remove_professorcourse($professor_id, $course_id);
        return ['message' => 'Course removed successfully'];
    }
}