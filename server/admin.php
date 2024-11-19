<?php

include "connection.php";
include "jwt.php";

$jwt = new JWT();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    $payload = $jwt->decode($data['jwt'] ?? '');
    if (empty($payload) || $payload['type_id'] !== 3) {
        echo json_encode([
            'success' => false,
            'message' => 'Not authorized'
        ]);
        exit;
    }

    switch ($action) {
        case 'view_users':
            viewUsers($connection);
            break;
        case 'create_instructor':
            createInstructor($connection, $data);
            break;
        case 'create_course':
            createCourse($connection, $data);
            break;
        case 'edit_course':
            editCourse($connection, $data);
            break;
        case 'delete_course':
            deleteCourse($connection, $data);
            break;
        case 'ban_user':
            banUser($connection, $data);
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

function viewUsers($connection)
{
    $query = "SELECT user_id, name, email, type_id FROM users";
    $result = $connection->query($query);

    if ($result) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode([
            'success' => true,
            'data' => $users
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch users'
        ]);
    }
}

function createInstructor($connection, $data)
{
    if (!isset($data['name'], $data['email'], $data['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Name, email, and password are required'
        ]);
        return;
    }

    $name = $data['name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $type_id = 2;

    $query = $connection->prepare("INSERT INTO users (name, email, password, type_id) VALUES (?, ?, ?, ?)");
    $query->bind_param("sssi", $name, $email, $password, $type_id);

    if ($query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Instructor created successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create instructor'
        ]);
    }
}

function createCourse($connection, $data)
{
    if (!isset($data['name'], $data['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Course name and instructor user_id are required'
        ]);
        return;
    }

    $name = $data['name'];
    $user_id = intval($data['user_id']);
    $streamlink = $data['streamlink'] ?? '';

    $query = $connection->prepare("INSERT INTO course (course_id, name, streamlink, user_id) VALUES (NULL, ?, ?, ?)");
    $query->bind_param("ssi", $name, $streamlink, $user_id);

    if ($query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Course created successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create course'
        ]);
    }
}

function editCourse($connection, $data)
{
    if (!isset($data['course_id'], $data['name'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Course ID and name are required'
        ]);
        return;
    }

    $course_id = intval($data['course_id']);
    $name = $data['name'];
    $streamlink = $data['streamlink'] ?? '';

    $query = $connection->prepare("UPDATE course SET name = ?, streamlink = ? WHERE course_id = ?");
    $query->bind_param("ssi", $name, $streamlink, $course_id);

    if ($query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Course updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update course'
        ]);
    }
}

function deleteCourse($connection, $data)
{
    if (!isset($data['course_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Course ID is required'
        ]);
        return;
    }

    $course_id = intval($data['course_id']);

    $query = $connection->prepare("DELETE FROM course WHERE course_id = ?");
    $query->bind_param("i", $course_id);

    if ($query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete course'
        ]);
    }
}

function banUser($connection, $data)
{
    if (!isset($data['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID is required'
        ]);
        return;
    }

    $user_id = intval($data['user_id']);

    $query = $connection->prepare("DELETE FROM users WHERE user_id = ?");
    $query->bind_param("i", $user_id);

    if ($query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'User banned successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to ban user'
        ]);
    }
}
