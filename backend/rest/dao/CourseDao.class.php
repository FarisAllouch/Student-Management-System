<?php

require_once __DIR__ . '/BaseDao.class.php';

class CourseDao extends BaseDao {
    public function __construct() {
        parent::__construct('course');
    }

    public function add_course($course) {
        return $this->insert('course', $course);
    }

    public function count_courses_paginated($search) {
        $query = "SELECT COUNT(*) As Count
                  FROM course 
                  WHERE LOWER(CourseName) LIKE CONCAT('%', :search, '%');";
        return $this->query_unique($query, [
            'search' => $search
        ]);
    }

    public function get_courses_paginated($offset, $limit, $search, $order_column, $order_direction) {
        $query = "SELECT *
                  FROM course 
                  WHERE LOWER(CourseName) LIKE CONCAT('%', :search, '%')
                  ORDER BY {$order_column} {$order_direction}
                  LIMIT {$offset}, {$limit}";
        return $this->query($query, [
            'search' => $search
        ]);
    }

    public function delete_course_by_id($id) {
        $query = "DELETE FROM course WHERE id = :id";
        $this->execute($query, [
            'id' => $id
        ]);
    } 

    public function get_course_by_id($course_id) {
        return $this->query_unique(
    "SELECT * FROM course WHERE id = :id", 
    [
                'id' => $course_id
            ]
        );    
    }

    public function edit_course($id, $course) {
        $query = "UPDATE course SET CourseName = :CourseName, id = :id
                  WHERE id = :id";
        $this->execute($query, [
            'CourseName' => $course['CourseName'],
            'id' => $id
        ]);
    }
}