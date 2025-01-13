<?php
include 'index.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sessionID = $_COOKIE['sessionID'] ?? null;
    $street = $_POST['street'] ?? null;
    $houseNumber = $_POST['house_number'] ?? null;
    $city = $_POST['city'] ?? null;
    $postalCode = $_POST['postal_code'] ?? null;

    if (!$sessionID) {
        $response['status'] = 'expired'; 
        $response['message'] = 'Invalid sessionID.';
        echo json_encode($response);
        exit;
    }

    if (!$street || !$houseNumber || !$city || !$postalCode) {
        $response['message'] = 'Please fill in all fields.';
        echo json_encode($response);
        exit;
    }

    if (strlen($postalCode) !== 5 || !preg_match('/^\d{5}$/', $postalCode)) {
        $response['message'] = 'Invalid postal code for Slovakia. It should be a 5-digit number.';
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

            $stmt = $conn->prepare('SELECT COUNT(*) FROM user_addresses WHERE user_id = ?');
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($addressCount);
            $stmt->fetch();
            $stmt->close();

            if ($addressCount > 0) {
                $stmt = $conn->prepare('UPDATE user_addresses SET street = ?, house_number = ?, city = ?, postal_code = ? WHERE user_id = ?');
                $stmt->bind_param('ssssi', $street, $houseNumber, $city, $postalCode, $user_id);
            } else {
                $stmt = $conn->prepare('INSERT INTO user_addresses (user_id, street, house_number, city, postal_code, country_id) VALUES (?, ?, ?, ?, ?, 1)');
                $stmt->bind_param('issss', $user_id, $street, $houseNumber, $city, $postalCode);
            }

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Address updated successfully.';
                $response['sessionId'] = $sessionID;
                $response['sessionIdExpirationDate'] = $new_expiration_time;
            } else {
                $response['message'] = 'Error: ' . $stmt->error;
            }
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