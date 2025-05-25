<?php

if (!function_exists('authorizeRole')) {
    function authorizeRole($requiredRole) {
        $user = Flight::get('user');
        if ($user->role !== $requiredRole) {
            Flight::halt(403, 'Access denied: insufficient privileges');
        }
    }
}

if (!function_exists('authorizeRoAles')) {
    function authorizeRoles($roles) {
        $user = Flight::get('user');
        if (!in_array($user->role, $roles)) {           
            Flight::halt(403, 'Forbidden: role not allowed');
        }
    }
}

if (!function_exists('authorizePermission')) {
    function authorizePermission($permission) {
        $user = Flight::get('user');
        if (!isset($user->permissions) || !in_array($permission, $user->permissions)) {
            Flight::halt(403, 'Access denied: permission missing');
        }
    }
}