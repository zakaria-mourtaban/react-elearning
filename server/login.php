<?php

include "connection.php";
include "jwt.php";

$jwt = new JWT();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['email']) && isset($data['password'])) {
		$email = $data['email'];
		$password = $data['password'];

		$query = $connection->prepare("SELECT user_id, name, email, password, type_id FROM users WHERE email = ?");
		$query->bind_param("s", $email);
		$query->execute();
		$result = $query->get_result();

		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();

			if (password_verify($password, $user['password'])) {
				$payload = [
					'id' => $user['user_id'],
					'type_id' => $user['type_id'],
					'name' => $user['name'],
					'email' => $user['email']
				];
				$token = $jwt->generate($payload);

				echo json_encode([
					'success' => true,
					'message' => 'Login successful',
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
} else {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Invalid Credentials'
	]);
}
