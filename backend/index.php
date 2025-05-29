<?php
require 'vendor/autoload.php';
require '../backend/helpers/auth_helpers.php';
require 'rest/routes/middleware_routes.php';
require 'rest/routes/course_routes.php';
require 'rest/routes/exam_routes.php';
require 'rest/routes/professor_routes.php';
require 'rest/routes/student_routes.php';
require 'rest/routes/professorcourse_routes.php';
require 'rest/routes/studentcourse_routes.php';
require 'rest/routes/grade_routes.php';
require 'rest/routes/auth_routes.php';


Flight::start();