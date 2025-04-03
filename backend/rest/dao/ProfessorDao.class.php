<?php

require_once __DIR__ . '/BaseDao.class.php';

class ProfessorDao extends BaseDao {
    public function __construct() {
        parent::__construct('professors');
    }

    public function add_professor($professor) {
        return $professor;
    }
}