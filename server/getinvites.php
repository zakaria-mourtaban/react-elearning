<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);
	$payload = $jwt->decode($data['jwt']);
	if ($payload == []) {
		echo json_encode([
			'success' => false,
			'message' => 'not authorized'
		]);
		exit;
	}
	$query = $connection->prepare("SELECT DISTINCT course.course_id, course.name, course.streamlink FROM invites JOIN course ON invites.course_id = course.course_id where invites.user_id = ?");
	$query->bind_param("i", $payload["id"]);
	$query->execute();
	$result = $query->get_result();
	$return = [];
	while ($row = $result->fetch_assoc()) {
		$return[] = $row;
	}
	echo json_encode([
		'success' => true,
		'message' => 'success',
		'invites' => json_encode($return)
	]);
	exit;
} else {
	echo json_encode([
		'success' => false,
		'message' => 'Invalid request method'
	]);
}
