<?php

require_once __DIR__ . "/../dao/StudentDao.class.php";

class StudentService {
    private $student_dao;

    public function __construct() {
        $this->student_dao = new StudentDao();
    }

    public function add_student($student) {
        $student['Password'] = password_hash($student['Password'], PASSWORD_BCRYPT);
        return $this->student_dao->add_student($student);
    }

    public function get_students_paginated($offset, $limit, $search, $order_column, $order_direction){
        $count = $this->student_dao->count_students_paginated($search)['Count'];
        $rows = $this->student_dao->get_students_paginated($offset, $limit, $search, $order_column, $order_direction);
        
        return [
            'count' => $count,
            'data' => $rows
        ];
    }

    public function delete_student_by_id ($student_id) {   
        $this->student_dao->delete_student_by_id($student_id);
    }

    public function get_student_by_id ($student_id) {
        return $this->student_dao->get_student_by_id($student_id);
    }

    public function edit_student($student) {
        return $this->student_dao->edit_student($student);
    }
    
    public function get_course_students($course_id) {
        return $this->student_dao->get_course_students($course_id);
    }
}