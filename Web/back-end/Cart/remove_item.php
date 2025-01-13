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
            $data = json_decode(file_get_contents('php://input'), true);
            $cartId = $data['cart_id'] ?? null;

            if ($cartId) {
                $check_sql = "SELECT * FROM cart WHERE cart_id = ? AND client_id = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("ii", $cartId, $response['user_id']);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    $delete_sql = "DELETE FROM cart WHERE cart_id = ? AND client_id = ?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("ii", $cartId, $response['user_id']);
                    $delete_stmt->execute();

                    if ($delete_stmt->affected_rows > 0) {
                        $response['status'] = 'success';
                        $response['message'] = 'Item removed successfully.';
                    } else {
                        $response['message'] = 'Failed to remove item from cart.';
                    }

                    $delete_stmt->close();
                } else {
                    $response['message'] = 'Invalid cart ID.';
                }

                $check_stmt->close();
            } else {
                $response['message'] = 'Cart ID is required.';
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