<?php
    include 'index.php';

    header('Content-Type: application/json');

    $session_id = $_POST['session_id'];

    $sql = "SELECT Id, Session_updated_at FROM users WHERE Session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['Id'];
        $session_updated_at = $row['Session_updated_at'];

        $current_time = new DateTime();
        $session_time = new DateTime($session_updated_at);
        $session_time->modify('+1 hour');

        if ($current_time < $session_time) {
            $update_sql = "UPDATE users SET session_updated_at = CURRENT_TIMESTAMP WHERE session_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $session_id);
            $update_stmt->execute();

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
                echo json_encode(['success' => true, 'data' => $user_data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No user data found.']);
            }

            $user_stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Session expired.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid session.']);
    }

    $stmt->close();
    $conn->close();
?>