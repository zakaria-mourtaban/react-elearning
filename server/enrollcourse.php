<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['course_id']) && isset($data['instructor_id'])) {
		$payload = json_decode($jwt->decode($data['jwt']),associative:true);
		if ($payload == null)
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

		$courseCheckQuery = $connection->prepare("SELECT * FROM course WHERE course_id = ? AND instructor_id = ?");
		$courseCheckQuery->bind_param("ii", $course_id, $instructor_id);
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

		if ($enrollmentResult->num_rows > 0) {
			echo json_encode([
				'success' => false,
				'message' => 'Student is already enrolled in this course'
			]);
			exit;
		}

		$enrollQuery = $connection->prepare("INSERT INTO course (course_id, instructor_id, student_id) VALUES (?, ?, ?)");
		$enrollQuery->bind_param("iii", $course_id, $instructor_id, $student_id);

		if ($enrollQuery->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'Student successfully enrolled in the course'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to enroll student in the course'
			]);
		}
	} else {
		echo json_encode([
			'success' => false,
			'message' => 'student_id, course_id, and instructor_id are required'
		]);
	}
} else {
	echo json_encode([
		'success' => false,
		'message' => 'Invalid request method'
	]);
}

?>