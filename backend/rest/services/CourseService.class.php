<?php

require_once __DIR__ . "/../dao/CourseDao.class.php";

class CourseService {
    private $course_dao;

    public function __construct() {
        $this->course_dao = new CourseDao();
    }
    public function add_course($course) {
        return $this->course_dao->add_course($course);
    }

    public function get_courses_paginated($offset, $limit, $search, $order_column, $order_direction){
        $count = $this->course_dao->count_courses_paginated($search)['Count'];
        $rows = $this->course_dao->get_courses_paginated($offset, $limit, $search, $order_column, $order_direction);
        
        return [
            'count' => $count,
            'data' => $rows
        ];
    }

    public function get_courses_paginated_student($offset, $limit, $search, $order_column, $order_direction, $studentId){
        $count = $this->course_dao->count_courses_paginated_student($search, $studentId)['Count'];
        $rows = $this->course_dao->get_courses_paginated_student($offset, $limit, $search, $order_column, $order_direction, $studentId);
        
        return [
            'count' => $count,
            'data' => $rows
        ];
    }

    public function get_courses_paginated_professor($offset, $limit, $search, $order_column, $order_direction, $professorId){
        $count = $this->course_dao->count_courses_paginated_professor($search, $professorId)['Count'];
        $rows = $this->course_dao->get_courses_paginated_professor($offset, $limit, $search, $order_column, $order_direction, $professorId);
        
        return [
            'count' => $count,
            'data' => $rows
        ];
    }

    public function delete_course_by_id ($course_id) {   
        $this->course_dao->delete_course_by_id($course_id);
    }

    public function get_course_by_id ($course_id) {
        return $this->course_dao->get_course_by_id($course_id);
    }

    public function edit_course($course) {
        return $this->course_dao->edit_course($course);
    }   

    public function get_course_exams($course_id) {
        return $this->course_dao->get_course_exams($course_id);
    }

    public function get_all_courses(){
        return $this->course_dao->get_all_courses();
    }
}