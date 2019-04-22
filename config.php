<?php
// mysql
if($_SERVER['SERVER_NAME'] === 'trainzland'
    || $_SERVER['SERVER_NAME'] === 'localhost'
    || $_SERVER['SERVER_NAME'] === 'fc') {
    $host = "127.0.0.1";
    $username = "root";
    $password = "";
    $db_name = "trainz";
} else{
    $host = "localhost";
    $username = "root";
    $password = "";
    $db_name = "test";
    ini_set( "display_errors", 0);
}

$recaptcha_key = "";
$recaptcha_secret = "";
