<?php 
//error_reporting(0);

date_default_timezone_set('Europe/Istanbul');

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "phppro";

try {
    $db = new PDO("mysql:host=".$db_server.";dbname=".$db_name.";charset=utf8", $db_user, $db_pass);
} catch ( PDOException $e ){
    print $e->getMessage();
}


?>