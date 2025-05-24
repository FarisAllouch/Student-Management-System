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
                  WHERE CourseName LIKE CONCAT('%', :search, '%')
                  ORDER BY {$order_column} {$order_direction}
                  LIMIT {$offset}, {$limit}";
        return $this->query($query, [
            'search' => $search
        ]);
    }

    public function delete_course_by_id($id) {
        $query = "DELETE FROM coursegrade WHERE StudentCourseId IN (
            SELECT id FROM studentcourse WHERE CourseID = :id
        )";
        $this->execute($query, [
            'id' => $id
        ]);

        $query = "DELETE FROM studentcourse WHERE CourseID = :id";
        $this->execute($query, [
            'id' => $id
        ]);

        $query = "DELETE FROM professorcourse WHERE CourseID = :id";
        $this->execute($query, [
            'id' => $id
        ]);

        $query = "DELETE FROM courseexams WHERE CourseId = :id";
        $this->execute($query, [
            'id' => $id
        ]);

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

    public function edit_course($course) {
        $query = "UPDATE course SET CourseName = :CourseName, CourseCode = :CourseCode WHERE id = :id";
        return $this->execute($query, [
            'CourseName' => $course['CourseName'],
            'CourseCode' => $course['CourseCode'],
            'id' => $course['id']
        ]);
    }

    public function get_course_exams($course_id) {
        $query = "SELECT ExamId as id, ExamName FROM courseexams WHERE CourseId = :course_id";
        return $this->query($query, ['course_id' => $course_id]);
    }

    public function get_all_courses() {
        $query = "SELECT * FROM course;";
        return $this->query($query,[]);
    }
}