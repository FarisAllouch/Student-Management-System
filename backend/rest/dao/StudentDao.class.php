<?php
class StudentDao extends BaseDao {
    public function __construct() {
        parent::__construct('student');
    }

    public function add_student($student) {
        return $this->insert('student', $student);
    }

    public function count_students_paginated($search) {
        $query = "SELECT COUNT(*) As Count
                  FROM student
                  WHERE LOWER(FirstName) LIKE CONCAT('%', :search, '%');";
        return $this->query_unique($query, [
            'search' => $search
        ]);
    }

    public function get_students_paginated($offset, $limit, $search, $order_column, $order_direction) {
        $query = "SELECT *
                  FROM student 
                  WHERE FirstName LIKE CONCAT('%', :search, '%')
                  ORDER BY {$order_column} {$order_direction}
                  LIMIT {$offset}, {$limit}";
        return $this->query($query, [
            'search' => $search
        ]);
    }

    public function delete_student_by_id($id) {
        $query = "DELETE FROM student WHERE id = :id";
        $this->execute($query, [
            'id' => $id
        ]);
    } 

    public function get_student_by_id($student_id) {
        return $this->query_unique(
    "SELECT * FROM student WHERE id = :id", 
    [
                'id' => $student_id
            ]
        );    
    }

    public function edit_student($student) {
        $query = "UPDATE student SET FirstName= :FirstName, LastName= :LastName, Email = :Email, Password = :Password WHERE id = :id";
        return $this->execute($query, [
            'FirstName' => $student['FirstName'],
            'LastName' => $student['LastName'],
            'Email' => $student['Email'],
            'Password' => $student['Password'],
            'id' => $student['id']
        ]);
    }
}