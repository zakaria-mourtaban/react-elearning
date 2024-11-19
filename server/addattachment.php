<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['assignment_id']) && isset($data['link'])) {
		$payload = $jwt->decode($data['jwt']);
		if ($payload == [])
		{
			echo json_encode([
				'success' => false,
				'message' => 'not authorized'
			]);
			exit;
		}
		$user_id = intval($payload['id']);
		$assignment_id = intval($data['assignment_id']);
		$link = $data['link'];

		$assignmentCheck = $connection->prepare("SELECT * from assignment where assignment_id = ? and user_id = ?");
		$assignmentCheck->bind_param("ii",$assignment_id, $user_id);
		$assignmentCheck->execute();
		$assignmentResult = $assignmentCheck->get_result();

		if ($assignmentResult->num_rows === 0) {
			echo json_encode([
				'success' => false,
				'message' => 'assignment not found'
			]);
			exit;
		}

		$postQuery = $connection->prepare("INSERT INTO attachment (assignment_id, link, user_id) VALUES (?, ?, ?)");
		$postQuery->bind_param("isi", $assignment_id, $link, $user_id);

		if ($postQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'comment posted'
			]);
		} else {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'failed to comment'
			]);
		}
	} else {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'message' => 'course_id, instructor_id, assignment_id, comment, private are required'
		]);
	}
} else {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Invalid request method'
	]);
}