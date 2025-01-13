<?php
include 'index.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sessionID = $_COOKIE['sessionID'] ?? null;

    if (!$sessionID) {
        $response['status'] = 'expired'; 
        $response['message'] = 'SessionID not valid.';
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare('SELECT Id, Session_updated_at FROM users WHERE Session_id = ?');
    $stmt->bind_param("s", $sessionID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['Id'];
        $session_updated_at = $row['Session_updated_at'];

        $current_time = new DateTime();
        $session_time = new DateTime($session_updated_at);

        if ($current_time < $session_time) {
            $new_expiration_time = time() + 3600;
            $update_sql = "UPDATE users SET session_updated_at = FROM_UNIXTIME(?) WHERE Session_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $new_expiration_time, $sessionID);
            $update_stmt->execute();

            setcookie('sessionID', $sessionID, $new_expiration_time, "/", "", true, true);

            $user_sql = "SELECT 
                            u.username,
                            u.email,
                            r.name AS role,
                            ua.street,
                            ua.house_number,
                            ua.city,
                            ua.postal_code,
                            c.country_name AS country
                        FROM 
                            users u
                        JOIN 
                            user_roles r ON r.id = u.role_id
                        LEFT JOIN 
                            user_addresses ua ON ua.user_id = u.id
                        LEFT JOIN 
                            countries c ON c.country_id = ua.country_id
                        WHERE 
                            u.id = ?";

            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();

            if ($user_result->num_rows > 0) {
                $user_data = $user_result->fetch_assoc();
                $response['status'] = 'success'; 
                $response['data'] = $user_data;
                $response['sessionId'] = $sessionID;
                $response['sessionIdExpirationDate'] = $new_expiration_time;
                echo json_encode($response); 
                exit;
            } else {
                $response['message'] = "No user data found.";
                echo json_encode($response);
                exit;
            }

            $user_stmt->close();
        } else {
            $response['status'] = 'expired'; 
            $response['message'] = "SessionID expired.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response['status'] = 'expired'; 
        $response['message'] = "Invalid SessionID.";
        echo json_encode($response);
        exit;
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>