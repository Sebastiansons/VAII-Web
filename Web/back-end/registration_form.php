<?php
include 'index.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || strlen($username) > 30 || preg_match('/[^a-zA-Z0-9]/', $username)) {
        $response['message'] = "Invalid username.";
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format.";
        echo json_encode($response);
        exit;
    }

    if (strlen($password) < 10) {
        $response['message'] = "Password must be at least 10 characters long.";
        echo json_encode($response);
        exit;
    }

    if ($password !== $confirm_password) {
        $response['message'] = "Passwords do not match.";
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("SELECT Id FROM Users WHERE Username = ? OR Email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['message'] = "Username or email already exists.";
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Users (Username, Email, Password) VALUES (?, ?, ?)");
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = "Registration successful!";
    } else {
        $response['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>