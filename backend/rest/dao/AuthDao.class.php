<?php

require_once __DIR__ . '/BaseDao.class.php';

class AuthDao extends BaseDao {
    public function __construct() {
        parent::__construct(null);
    }


    public function get_user_by_email($email) {
        $roles = ['professor', 'student', 'admin'];
        foreach($roles as $role) {
            $query = "SELECT *, '$role' as role FROM $role WHERE Email = :Email";
            $user = $this->query_unique($query, ['Email' => $email]);

            if($user) {
                return $user;
            }
        }
        
        return false;
    }
}