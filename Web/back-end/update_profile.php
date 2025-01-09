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

    $stmt = $conn->prepare('SELECT id, Session_updated_at FROM users WHERE Session_id = ?');
    $stmt->bind_param('s', $sessionID);
    $stmt->execute();
    $stmt->bind_result($userID, $sessionUpdatedAt);
    $stmt->fetch();
    $stmt->close();

    if (!$userID) {
        $response['message'] = 'Invalid session ID.';
        echo json_encode($response);
        exit;
    }

    $currentDate = new DateTime();
    $sessionUpdatedAt = new DateTime($sessionUpdatedAt);
    $sessionUpdatedAt->modify('+1 hour');

    if ($currentDate > $sessionUpdatedAt) {
        $response['message'] = 'Session expired.';
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare('UPDATE users SET Session_updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare('SELECT COUNT(*) FROM user_addresses WHERE user_id = ?');
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $stmt->bind_result($addressCount);
    $stmt->fetch();
    $stmt->close();

    if ($addressCount > 0) {
        $stmt = $conn->prepare('UPDATE user_addresses SET street = ?, house_number = ?, city = ?, postal_code = ? WHERE user_id = ?');
        $stmt->bind_param('ssssi', $street, $houseNumber, $city, $postalCode, $userID);
    } else {
        $stmt = $conn->prepare('INSERT INTO user_addresses (user_id, street, house_number, city, postal_code, country_id) VALUES (?, ?, ?, ?, ?, 1)');
        $stmt->bind_param('issss', $userID, $street, $houseNumber, $city, $postalCode);
    }

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Address updated successfully.';
    } else {
        $response['message'] = 'Error: ' . $stmt->error;
        error_log('SQL Error: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}

?>