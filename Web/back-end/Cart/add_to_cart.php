<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sessionID = $_COOKIE['sessionID'] ?? null;

    if ($sessionID) {
        $response = CheckSession($conn);

        if (isset($response['role']) && $response['role'] == 'Customer') {
            $itemID = $_POST['ItemID'] ?? null;
            $clientID = $response['user_id'] ?? null;

            if ($itemID && $clientID) {
                $insert_sql = "INSERT INTO cart (client_id, item_id) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ii", $clientID, $itemID);
                $insert_stmt->execute();

                if ($insert_stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Item added to cart successfully.';
                } else {
                    $response['message'] = 'Failed to add item to cart.';
                }

                $insert_stmt->close();
            } else {
                $response['message'] = 'Item ID and Client ID are required.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'You do not have permission.';
        }
    } else {
        $response['message'] = 'SessionID is required.';
    }

    $conn->close();
}

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>