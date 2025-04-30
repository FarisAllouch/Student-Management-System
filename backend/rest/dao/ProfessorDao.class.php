<?php
class ProfessorDao extends BaseDao {
    public function __construct() {
        parent::__construct('professor');
    }

    public function add_professor($professor) {
        return $this->insert('professor', $professor);
    }

    public function count_professors_paginated($search) {
        $query = "SELECT COUNT(*) As Count
                  FROM professor
                  WHERE LOWER(FullName) LIKE CONCAT('%', :search, '%');";
        return $this->query_unique($query, [
            'search' => $search
        ]);
    }

    public function get_professors_paginated($offset, $limit, $search, $order_column, $order_direction) {
        $query = "SELECT *
                  FROM professor 
                  WHERE FullName LIKE CONCAT('%', :search, '%')
                  ORDER BY {$order_column} {$order_direction}
                  LIMIT {$offset}, {$limit}";
        return $this->query($query, [
            'search' => $search
        ]);
    }

    public function delete_professor_by_id($id) {
        $query = "DELETE FROM professor WHERE id = :id";
        $this->execute($query, [
            'id' => $id
        ]);
    } 

    public function get_professor_by_id($professor_id) {
        return $this->query_unique(
    "SELECT * FROM professor WHERE id = :id", 
    [
                'id' => $professor_id
            ]
        );    
    }

    public function edit_professor($professor) {
        $query = "UPDATE professor SET FullName = :FullName, Email = :Email, Password = :Password WHERE id = :id";
        return $this->execute($query, [
            'FullName' => $professor['FullName'],
            'Email' => $professor['Email'],
            'Password' => $professor['Password'],
            'id' => $professor['id']
        ]);
    }
}