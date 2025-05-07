<?php

require_once __DIR__ . '/../dao/StudentCourseDao.class.php';

class StudentCourseService {
    private $studentcourse_dao;

    public function __construct(){
        $this->studentcourse_dao = new StudentCourseDao();
    }

    public function get_studentcourse($student_id) {
        return $this->studentcourse_dao->get_studentcourse($student_id);
    }

    public function get_studentcourse_ass($student_id) {
        return $this->studentcourse_dao->get_studentcourse_ass($student_id);
    }

    public function add_studentcourse($course){
        $this->studentcourse_dao->add_studentcourse($course['StudentId'], $course['CourseId']);
        return ['message' => 'Course assigned successfully'];
    }

    public function remove_studentcourse($student_id, $course_id) {
        $this->studentcourse_dao->remove_studentcourse($student_id, $course_id);
        return ['message' => 'Course removed successfully'];
    }
} 