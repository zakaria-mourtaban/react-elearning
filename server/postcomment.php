<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['course_id']) && isset($data['instructor_id']) && isset($data['assignment_id']) && isset($data['comment']) && isset($data['private'])) {
		$payload = $jwt->decode($data['jwt']);
		if ($payload == [])
		{
			echo json_encode([
				'success' => false,
				'message' => 'not authorized'
			]);
			exit;
		}
		$student_id = intval($payload['id']);
		$course_id = intval($data['course_id']);
		$instructor_id = intval($data['instructor_id']);
		$assignmenet_id = intval($data['assignment_id']);
		$comment = $data['comment'];
		$private = $data['private'];

		$courseCheckQuery = $connection->prepare("SELECT * FROM course WHERE course_id = ? AND instructor_id = ?");
		$courseCheckQuery->bind_param("ii", $course_id, $instructor_id, $student_id);
		$courseCheckQuery->execute();
		$courseResult = $courseCheckQuery->get_result();

		if ($courseResult->num_rows === 0) {
			echo json_encode([
				'success' => false,
				'message' => 'Course not found or instructor mismatch'
			]);
			exit;
		}

		$enrollmentCheckQuery = $connection->prepare("SELECT * FROM course WHERE course_id = ? AND student_id = ?");
		$enrollmentCheckQuery->bind_param("ii", $course_id, $student_id);
		$enrollmentCheckQuery->execute();
		$enrollmentResult = $enrollmentCheckQuery->get_result();

		if ($enrollmentResult->num_rows === 0) {
			echo json_encode([
				'success' => false,
				'message' => 'Student is not enrolled in this course'
			]);
			exit;
		}
		$postQuery = $connection->prepare("INSERT INTO comments (assingment_id, comment, private) VALUES (?, ?, ?)");
		$postQuery->bind_param("isi", $assignmenet_id, $comment, $private);

		if ($enrollQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'comment posted'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'failed to comment'
			]);
		}
	} else {
		echo json_encode([
			'success' => false,
			'message' => 'course_id, instructor_id, assignment_id, comment, private are required'
		]);
	}
} else {
	echo json_encode([
		'success' => false,
		'message' => 'Invalid request method'
	]);
}