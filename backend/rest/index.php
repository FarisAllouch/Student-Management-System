<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/routes/student_routes.php';
require_once __DIR__ . '/routes/professor_routes.php';
require_once __DIR__ . '/routes/course_routes.php';
require_once __DIR__ . '/routes/grade_routes.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Error handling
Flight::map('error', function(Exception $ex){
    Flight::json(['message' => $ex->getMessage()], 500);
});

Flight::map('notFound', function(){
    Flight::json(['message' => 'Route not found'], 404);
});

Flight::start(); 