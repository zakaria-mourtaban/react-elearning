<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['course_id'])) {
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
		$course_id = intval($data['course_id']);

		$courseCheckQuery = $connection->prepare("SELECT * FROM course WHERE course_id = ?");
		$courseCheckQuery->bind_param("i", $course_id);
		$courseCheckQuery->execute();
		$courseResult = $courseCheckQuery->get_result();

		if ($courseResult->num_rows === 0) {
			echo json_encode([
				'success' => false,
				'message' => 'Course not found'
			]);
			exit;
		}

		$deleteQuery = $connection->prepare('DELETE FROM invites WHERE course_id = ? AND user_id = ?;');
		$deleteQuery->bind_param("ii", $course_id, $user_id);
		if ($deleteQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'Rejected Successfully'
			]);
		} else {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'Reject failed'
			]);
		}
	} else {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'message' => 'Enroll Failed2'
		]);
	}
} else {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Enroll Failed3'
	]);
}

?>