<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['announcement']) && isset($data['course_id'])) {
		$payload = $jwt->decode($data['jwt']);
		if ($payload == [] || $payload["type_id"] < 2) {
			echo json_encode([
				'success' => false,
				'message' => 'not authorized'
			]);
			exit;
		}
		$user_id = intval($payload['id']) ?? null;
		$announcement_id = $data['announcement_id'] ?? null;
		$announcement = $data['announcement'] ?? null;
		$course_id = $data['course_id'] ?? null;

		if ($announcement_id == null) {
			$postQuery = $connection->prepare("INSERT INTO announcement (announcement, course_id, announcement_id) VALUES (?, ?, ?)");
			$postQuery->bind_param("sii", $announcement, $course_id, $announcement_id);
		} else {
			$postQuery = $connection->prepare("UPDATE announcement SET announcement = ?,course_id = ?, announcement_id = ? WHERE announcement_id = ?");
			$postQuery->bind_param("siii", $announcement, $course_id, $announcement_id, $announcement_id);
		}

		if ($postQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'announcement posted'
			]);
		} else {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'failed to announcement'
			]);
		}
	} else {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'message' => 'course_id, instructor_id, announcement_id, announcement, private are required'
		]);
	}
} else {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Invalid request method'
	]);
}