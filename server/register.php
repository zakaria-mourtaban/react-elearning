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

        $checkQuery = $connection->prepare("SELECT id FROM student WHERE email = ?");
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
        $insertQuery = $connection->prepare("INSERT INTO student (name, email, password) VALUES (?, ?, ?)");
        $insertQuery->bind_param("sss", $name, $email, $passwordHash);

        if ($insertQuery->execute()) {
            $userId = $insertQuery->insert_id;
            $payload = [
                'id' => $userId,
                'name' => $name,
                'email' => $email
            ];
            $token = $jwt->generate($payload);

            echo json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'token' => $token
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to register user'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Name, email, and password are required'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}