<?php

//Set the reporting 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config {
    public static function DB_NAME() {
        return Config::get_env("DB_NAME", "sisweb");
    }

    public static function DB_PORT() {
        return Config::get_env("DB_PORT", 3306);
    }
    
    public static function DB_USER() {
        return Config::get_env("DB_USER", 'root');
    }   

    public static function DB_PASSWORD() {
        return Config::get_env("DB_PASSWORD", 'palestine');
    }   

    public static function DB_HOST() {
        return Config::get_env("DB_HOST", 'localhost');
    }

    public static function JWT_SECRET() {
        return Config::get_env("JWT_SECRET", '64e7%&M7ix%Ph%3v3Ai&,]67-5k5j?');
    }      
    public static function get_env($name, $default) {
        return isset($_ENV[$name])  && trim($_ENV[$name]) !=  " " ? $_ENV[$name] : $default;
    }
}

// //Database access credentials
// define('DB_NAME', 'sisweb');
// define('DB_PORT', 3306);
// define('DB_USER','root');
// define('DB_PASSWORD', 'palestine');
// define('DB_HOST','localhost'); //localhost

//JWT Secret

// define('JWT_SECRET', '64e7%&M7ix%Ph%3v3Ai&,]67-5k5j?');
