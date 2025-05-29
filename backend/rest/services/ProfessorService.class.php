<?php

require_once __DIR__ . "/../dao/ProfessorDao.class.php";

class ProfessorService {
    private $professor_dao;

    public function __construct() {
        $this->professor_dao = new ProfessorDao();
    }

    public function add_professor($professor) {
        $professor['Password'] = password_hash($professor['Password'], PASSWORD_BCRYPT);
        return $this->professor_dao->add_professor($professor);
    }

    public function get_professors_paginated($offset, $limit, $search, $order_column, $order_direction){
        $count = $this->professor_dao->count_professors_paginated($search)['Count'];
        $rows = $this->professor_dao->get_professors_paginated($offset, $limit, $search, $order_column, $order_direction);
        
        return [
            'count' => $count,
            'data' => $rows
        ];
    }

    public function delete_professor_by_id ($professor_id) {   
        $this->professor_dao->delete_professor_by_id($professor_id);
    }

    public function get_professor_by_id ($professor_id) {
        return $this->professor_dao->get_professor_by_id($professor_id);
    }

    public function edit_professor($professor) {
        return $this->professor_dao->edit_professor($professor);
    }   
}