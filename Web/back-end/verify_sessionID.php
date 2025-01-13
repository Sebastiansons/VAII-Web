<?php
	function CheckSession($conn) {
        $response = array('status' => 'error', 'message' => '');

        $sessionID = $_COOKIE['sessionID'] ?? null;

        if (!$sessionID) {
            $response['status'] = 'expired'; 
            $response['message'] = 'SessionID not valid.';
            return $response;
        }

        $stmt = $conn->prepare('SELECT u.Id, u.Session_updated_at, r.Name AS Role FROM users u JOIN user_roles r on u.Role_id = r.Id WHERE u.Session_id = ?');
        $stmt->bind_param("s", $sessionID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['Id'];
            $session_updated_at = $row['Session_updated_at'];
            $role = $row['Role'];

            $current_time = new DateTime();
            $session_time = new DateTime($session_updated_at);

            if ($current_time < $session_time) {
                $new_expiration_time = time() + 3600;
                $update_sql = "UPDATE users SET session_updated_at = FROM_UNIXTIME(?) WHERE Session_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("is", $new_expiration_time, $sessionID);
                $update_stmt->execute();

                setcookie('sessionID', $sessionID, $new_expiration_time, "/", "", true, true);

                $response['status'] = 'success'; 
                $response['user_id'] = $user_id;
                $response['sessionId'] = $sessionID;
                $response['role'] = $role;
                $response['sessionIdExpirationDate'] = $new_expiration_time;
            } else {
                $response['status'] = 'expired'; 
                $response['message'] = "SessionID expired.";
            }
        } else {
            $response['status'] = 'expired'; 
            $response['message'] = "Invalid SessionID.";
        }

        $stmt->close();
        return $response;
    }
?>