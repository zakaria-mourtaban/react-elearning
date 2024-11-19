<?php

include "connection.php";
include "jwt.php";

$jwt = new JWT();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['name']) && isset($data['email']) && isset($data['password'])) {
		$name = trim($data['name']);
		$email = trim($data['email']);
		$password = trim($data['password']);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid email format'
			]);
			exit;
		}

		$checkQuery = $connection->prepare("SELECT user_id FROM users WHERE email = ?");
		$checkQuery->bind_param("s", $email);
		$checkQuery->execute();
		$checkQuery->store_result();

		if ($checkQuery->num_rows > 0) {
			echo json_encode([
				'success' => false,
				'message' => 'User already registered with this email'
			]);
			exit;
		}

		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$insertQuery = $connection->prepare("INSERT INTO users (name, email, password, type_id) VALUES (?, ?, ?, 1)");
		$insertQuery->bind_param("sss", $name, $email, $passwordHash);

		if ($insertQuery->execute()) {
			$userId = $insertQuery->insert_id;
			$payload = [
				'id' => $userId,
				'name' => $name,
				'email' => $email,
				'type_id' => 1,
			];
			$token = $jwt->generate($payload);

			echo json_encode([
				'success' => true,
				'message' => 'Registration successful',
				'token' => $token
			]);
		} else {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'Invalid Credentials'
			]);
		}
	} else {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'message' => 'Invalid Credentials'
		]);
	}
} else {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Invalid Credentials'
	]);
}