<?php

include "connection.php"; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['student_id']) && isset($data['course_id'])) {
        $student_id = intval($data['student_id']);
        $course_id = intval($data['course_id']);

        // Check if the student is enrolled in the course
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

        // Drop the student from the course
        $dropQuery = $connection->prepare("DELETE FROM course WHERE course_id = ? AND student_id = ?");
        $dropQuery->bind_param("ii", $course_id, $student_id);

        if ($dropQuery->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Student successfully dropped from the course'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to drop the student from the course'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'student_id and course_id are required'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

?>
