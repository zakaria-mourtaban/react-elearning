<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['course_id']) && isset($data['student_id'])) {
		$payload = $jwt->decode($data['jwt']);
		if ($payload == [] || $payload["type_id"] < 2) {
			echo json_encode([
				'success' => false,
				'message' => 'not authorized'
			]);
			exit;
		}
		$invited = $payload['invited'] ?? null;
		$user_id = intval($payload['id']) ?? null;
		$course_id = $data['course_id'] ?? null;
		$student_id = intval($data['student_id']) ?? null;
		if ($invited == null) {
			$postQuery = $connection->prepare("INSERT INTO invites (course_id, user_id) VALUES (?, ?)");
			$postQuery->bind_param("ii",$course_id, $student_id);
		} else {
			$postQuery = $connection->prepare("DELETE FROM invites WHERE user_id = ? and course_id = ?");
			$postQuery->bind_param("ii",$course_id, $student_id);
		}

		if ($postQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'invite sent'
			]);
		} else {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'failed to invite'
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