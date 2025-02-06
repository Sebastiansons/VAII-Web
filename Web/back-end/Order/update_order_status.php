<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

$sessionID = $_COOKIE['sessionID'] ?? null;

if ($sessionID) {
    $response = CheckSession($conn);

    if (isset($response['role'])) {
        $role = $response['role'];
        $clientID = $response['user_id'] ?? null;

        if ($role === 'Support') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $orderID = $_POST['orderID'];
                $newStatus = $_POST['status'];

                // Validate the new status
                $validStatuses = ['Created', 'Processing', 'Sent', 'Canceled'];
                if (!in_array($newStatus, $validStatuses)) {
                    $response['message'] = 'Invalid status value';
                    echo json_encode($response);
                    exit;
                }

                // Select the statusID for the new status
                $stmt = $conn->prepare("SELECT statusID FROM order_status WHERE statusName = ?");
                $stmt->bind_param('s', $newStatus);
                $stmt->execute();
                $stmt->bind_result($statusID);
                $stmt->fetch();
                $stmt->close();

                if ($statusID) {
                    // Update the order status in the database
                    $stmt = $conn->prepare("UPDATE orders SET statusID = ? WHERE orderID = ?");
                    $stmt->bind_param('is', $statusID, $orderID);

                    if ($stmt->execute()) {
                        $response['status'] = 'success';
                    } else {
                        $response['message'] = 'Failed to update order status';
                    }

                    $stmt->close();
                } else {
                    $response['message'] = 'Invalid status value';
                }
            } else {
                $response['message'] = 'Invalid request method';
            }
        } else {
            $response['message'] = 'You do not have permission.';
        }
    } else {
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