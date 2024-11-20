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
		$courseAssoc = $courseResult->fetch_assoc();
		$courseName = $courseAssoc['name'];
		$courseStreamlink = $courseAssoc['streamlink'];
		$enrollmentCheckQuery = $connection->prepare("SELECT * FROM course WHERE course_id = ? AND user_id = ?");
		$enrollmentCheckQuery->bind_param("ii", $course_id, $user_id);
		$enrollmentCheckQuery->execute();
		$enrollmentResult = $enrollmentCheckQuery->get_result();

		if ($enrollmentResult->num_rows > 0) {
			echo json_encode([
				'success' => false,
				'message' => 'already enrolled in this course'
			]);
			exit;
		}

		$enrollQuery = $connection->prepare("INSERT INTO course (course_id, user_id, name, streamlink) VALUES (?, ?, ?, ?)");
		$enrollQuery->bind_param("iiss", $course_id, $user_id, $courseName, $courseStreamlink);

		if ($enrollQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'Student successfully enrolled in the course'
			]);
		} else {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'Enroll Failed1'
			]);
		}
		$deleteQuery = $connection->prepare('DELETE FROM invites WHERE course_id = ? AND user_id = ?;');
		$deleteQuery->bind_param("ii", $course_id, $user_id);
		$deleteQuery->execute();
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