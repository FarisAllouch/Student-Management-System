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
        // First delete all grades for this student
        $query = "DELETE FROM coursegrade WHERE StudentCourseId IN (
            SELECT id FROM studentcourse WHERE StudentId = :id
        )";
        $this->execute($query, ['id' => $id]);

        // Then delete all course enrollments
        $query = "DELETE FROM studentcourse WHERE StudentId = :id";
        $this->execute($query, ['id' => $id]);

        // Finally delete the student
        $query = "DELETE FROM student WHERE id = :id";
        $this->execute($query, ['id' => $id]);
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

    public function get_course_students($course_id) {
        $query = "SELECT s.*, 
                 GROUP_CONCAT(CONCAT(cg.Grade, '% (', (cg.Grade * ce.Weight / 100), ' points)') SEPARATOR ', ') as Grades,
                 SUM(cg.Grade * ce.Weight / 100) as TotalPoints,
                 SUM(ce.Weight) as TotalWeight
                 FROM student s
                 JOIN studentcourse sc ON s.id = sc.StudentId
                 LEFT JOIN coursegrade cg ON cg.StudentCourseId = sc.id
                 LEFT JOIN courseexams ce ON cg.ExamId = ce.ExamId
                 WHERE sc.CourseID = :course_id
                 GROUP BY s.id";
        return $this->query($query, [
            'course_id' => $course_id
        ]);
    }
}