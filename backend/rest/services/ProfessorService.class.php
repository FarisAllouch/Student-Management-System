<?php

require_once __DIR__ . "/../dao/ProfessorDao.class.php";

class ProfessorService {
    private $professor_dao;

    public function __construct() {
        $this->professor_dao = new ProfessorDao();
    }
    public function add_professor($professor) {
        return $this->professor_dao->add_professor($professor);
    }
}