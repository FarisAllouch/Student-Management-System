<?php

require_once __DIR__ . "/../dao/ExamDao.class.php";
class ExamService {
    private $exam_dao;

    public function __construct() {
        $this->exam_dao = new ExamDao();
    }

    public function get_exams($course_id) {
        return $this->exam_dao->get_exams($course_id);
    }

    public function add_exam($exam) {
        return $this->exam_dao->add_exam($exam);
    }

    public function delete_exam_by_id($exam_id) {
        return $this->exam_dao->delete_exam_by_id($exam_id);
    }

    public function get_exam_by_id ($exam_id) {
        return $this->exam_dao->get_exam_by_id($exam_id);
    }

    public function edit_exam($exam) {
        return $this->exam_dao->edit_exam($exam);
    }   
}