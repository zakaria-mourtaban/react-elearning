<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');  
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$host = "localhost";
$dbuser = "root";
$pass = "";
$dbname = "elearningdb";

$connection = new mysqli($host, $dbuser, $pass, $dbname);

if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}