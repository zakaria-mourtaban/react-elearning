<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: Content-Type');

$host = "localhost";
$dbuser = "root";
$pass = "";
$dbname = "elearningdb";

$connection = new mysqli($host, $dbuser, $pass, $dbname);

if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);