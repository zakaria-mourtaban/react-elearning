<?php

include "connection.php"; // Include the database connection file
include "jwt.php";

$jwt = new JWT();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Check if the file and necessary fields exist
	if (isset($_POST['assignment_id']) && isset($_FILES['attachment']) && isset($_POST['jwt'])) {
		$payload = $jwt->decode($_POST['jwt']);
		if ($payload == []) {
			echo json_encode([
				'success' => false,
				'message' => 'Not authorized'
			]);
			exit;
		}

		// Extract required data
		$user_id = intval($payload['id']);
		$assignment_id = intval($_POST['assignment_id']);
		$file = $_FILES['attachment'];

		// File properties
		$uploadDir = 'uploads/attachments/';
		$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']; // Modify as needed
		$maxFileSize = 2 * 1024 * 1024; // 2 MB limit

		// Validate the uploaded file
		if (!in_array($file['type'], $allowedTypes)) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid file type. Allowed: JPEG, PNG, PDF.'
			]);
			exit;
		}
		if ($file['size'] > $maxFileSize) {
			echo json_encode([
				'success' => false,
				'message' => 'File size exceeds 2MB limit.'
			]);
			exit;
		}

		// Generate a unique file name and save the file
		$uniqueFileName = uniqid() . '_' . basename($file['name']);
		$targetPath = $uploadDir . $uniqueFileName;

		// Ensure upload directory exists
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0777, true);
		}

		if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to upload the file.'
			]);
			exit;
		}

		// Verify assignment ownership
		$assignmentCheck = $connection->prepare("SELECT * FROM assignment WHERE assignment_id = ?");
		$assignmentCheck->bind_param("ii", $assignment_id, $user_id);
		$assignmentCheck->execute();
		$assignmentResult = $assignmentCheck->get_result();

		if ($assignmentResult->num_rows === 0) {
			// Delete the uploaded file if the assignment is invalid
			unlink($targetPath);

			echo json_encode([
				'success' => false,
				'message' => 'Assignment not found or unauthorized'
			]);
			exit;
		}

		// Insert file attachment record into the database
		$addAttachment = $connection->prepare("INSERT INTO attachment (assignment_id, attachment, user_id) VALUES (?, ?, ?)");
		$addAttachment->bind_param("isi", $assignment_id, $uniqueFileName, $user_id);

		if ($addAttachment->execute()) {
			echo json_encode([
				'success' => true,
				'message' => 'Attachment added successfully',
				'file_path' => $targetPath
			]);
		} else {
			// Delete the uploaded file if DB insertion fails
			unlink($targetPath);

			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'Failed to add attachment.'
			]);
		}
	} else {
		// Missing required fields
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'message' => 'assignment_id, jwt, and attachment file are required.'
		]);
	}
} else {
	// Invalid request method
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Invalid request method'
	]);
}
