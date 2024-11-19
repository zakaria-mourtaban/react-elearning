<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['assignment']) && isset($data['course_id'])) {
		$payload = $jwt->decode($data['jwt']);
		if ($payload == [] || $payload["type_id"] < 2) {
			echo json_encode([
				'success' => false,
				'message' => 'not authorized'
			]);
			exit;
		}
		$user_id = intval($payload['id']) ?? null;
		$assignment_id = $data['assignment_id'] ?? null;
		$assignment = $data['assignment'] ?? null;
		$course_id = $data['course_id'] ?? null;

		if ($assignment_id == null) {
			$postQuery = $connection->prepare("INSERT INTO assignment (assignment, done, course_id, user_id) VALUES (?, 0, ?, ?)");
			$postQuery->bind_param("sii", $assignment, $course_id, $user_id);
		} else {
			$postQuery = $connection->prepare("UPDATE assignment SET assignment = ?,course_id = ?, user_id = ? WHERE assignment_id = ?");
			$postQuery->bind_param("siii", $assignment, $course_id, $user_id, $assignment_id);
		}

		if ($postQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'assignment posted'
			]);
		} else {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'failed to assignment'
			]);
		}
	} else {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'message' => 'course_id, instructor_id, assignment_id, assignment, private are required'
		]);
	}
} else {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Invalid request method'
	]);
}