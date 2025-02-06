<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

$sessionID = $_COOKIE['sessionID'] ?? null;

if ($sessionID) {
    $response = CheckSession($conn);

    if (isset($response['role']) && $response['role'] == 'Customer') {
        $clientID = $response['user_id'] ?? null;

        $data = json_decode(file_get_contents('php://input'), true);
        $cartID = $data['cart_id'] ?? null;
        $quantity = $data['quantity'] ?? null;

        if ($clientID && $cartID && $quantity !== null) {
            if ($quantity < 1) {
                $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND client_id = ?");
                $stmt->bind_param("ii", $cartID, $clientID);
            } elseif ($quantity > 10) {
                $response['message'] = 'Quantity cannot be more than 10';
                echo json_encode($response);
                exit;
            } else {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND client_id = ?");
                $stmt->bind_param("iii", $quantity, $cartID, $clientID);
            }

            if ($stmt->execute()) {
                $response['status'] = 'success';
            } else {
                $response['message'] = 'Failed to update quantity';
            }

            $stmt->close();
        } else {
            $response['message'] = 'Invalid input';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'You do not have permission.';
    }
} else {
    $response['message'] = 'SessionID is required.';
}

$conn->close();

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>