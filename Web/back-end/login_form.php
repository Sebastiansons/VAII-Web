<?php
include 'index.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = trim($_POST['username_or_email']);
    $password = trim($_POST['password']);

    if (empty($usernameOrEmail)) {
        $response['message'] = "Username or Email must not be empty.";
        echo json_encode($response);
        exit;
    }

    if (empty($password)) {
        $response['message'] = "Password must not be empty.";
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("SELECT u.Id, u.Password, u.Username, u.Balance, u.Role_id, r.Name AS RoleName 
                            FROM Users u 
                            JOIN user_roles r ON u.Role_id = r.Id 
                            WHERE u.Username = ? OR u.Email = ?");
    if ($stmt === false) {
        $response['message'] = "Prepare failed: " . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashed_password, $username, $balance, $roleId, $roleName);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $userId;
            
            $new_expiration_time = time() + 3600;
            $stmt = $conn->prepare("UPDATE Users SET Session_id = ?, Session_updated_at = FROM_UNIXTIME(?) WHERE Id = ?");
            if ($stmt === false) {
                $response['message'] = "Prepare failed: " . $conn->error;
                echo json_encode($response);
                exit;
            }

            $sessionId = session_id();
            $stmt->bind_param("sii", $sessionId, $new_expiration_time, $userId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                setcookie('sessionID', $sessionId, $new_expiration_time, "/", "", true, true);

                $cartStmt = $conn->prepare("SELECT SUM(quantity) AS stockcount FROM cart WHERE client_id = ?");
                if ($cartStmt === false) {
                    $response['message'] = "Prepare failed: " . $conn->error;
                    echo json_encode($response);
                    exit;
                }

                $cartStmt->bind_param("i", $userId);
                $cartStmt->execute();
                $cartStmt->bind_result($stockcount);
                $cartStmt->fetch();
                $cartStmt->close();

                $response['status'] = 'success';
                $response['message'] = "Login successful!";
                $response['session_id'] = $sessionId;
                $response['sessionIdExpirationDate'] = $new_expiration_time;
                $response['name'] = $username;
                $response['balance'] = number_format($balance, 2);
                $response['role'] = $roleName;
                $response['stockcount'] = $stockcount;
            } else {
                $response['message'] = "Failed to update session.";
            }
        } else {
            $response['message'] = "Invalid password.";
        }
    } else {
        $response['message'] = "Username or Email not found.";
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>