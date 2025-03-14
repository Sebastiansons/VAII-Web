<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'success', 'message' => '');

$sessionID = $_COOKIE['sessionID'] ?? null;

if ($sessionID) {
    $response = CheckSession($conn);

    if (isset($response['role']) && $response['role'] == 'Customer') { 
        $clientID = $response['user_id'] ?? null;

        if ($clientID) {
            $sql = "SELECT ua.street, ua.house_number, ua.city, ua.postal_code, c.country_name AS country
                    FROM user_addresses ua
                    JOIN countries c ON ua.country_id = c.country_id
                    WHERE ua.user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $clientID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $address = $result->fetch_assoc();
                $response['address'] = $address;
            } else {
                $response['message'] = 'Please set your address in your profile.';
            }

            $sql = "SELECT c.cart_id AS cart_id, si.Name AS name, si.ItemID, si.Description AS description, si.Price AS price, si.Image AS image, c.quantity AS quantity
                    FROM cart c
                    JOIN shopitems si ON c.item_id = si.ItemID
                    WHERE c.client_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $clientID);
            $stmt->execute();
            $result = $stmt->get_result();

            $cartItems = array();
            while ($row = $result->fetch_assoc()) {
                $row['image'] = explode(',', $row['image'])[0]; 
                $cartItems[] = $row;
            }

            $response['data'] = $cartItems;

            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Client ID is required.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'You do not have permission.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'SessionID is required.';
}

$conn->close();

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>
