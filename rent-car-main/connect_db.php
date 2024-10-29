<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log');

$host='localhost';
$user='root';
$pass='';
$dbname='rent_system';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die ("Unable to connect");
}

?>