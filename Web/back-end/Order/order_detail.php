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
        $orderID = $_GET['orderID'] ?? null;

        if ($orderID) {
            if ($role == 'Customer' && $clientID) {
                $sql = "SELECT * FROM orders WHERE orderID = ? AND clientID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $orderID, $clientID);
            } elseif ($role == 'Support') {
                $sql = "SELECT * FROM orders WHERE orderID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $orderID);
            } else {
                $response['message'] = 'You do not have permission.';
                echo json_encode($response);
                exit;
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();

            if ($order) {
                $itemIDs = explode(',', $order['itemIDs']);
                $quantities = explode(',', $order['quantities']);
                $items = [];

                foreach ($itemIDs as $index => $itemID) {
                    $item_sql = "SELECT * FROM shopitems WHERE ItemID = ?";
                    $item_stmt = $conn->prepare($item_sql);
                    $item_stmt->bind_param("i", $itemID);
                    $item_stmt->execute();
                    $item_result = $item_stmt->get_result();
                    $item = $item_result->fetch_assoc();
                    $item['quantity'] = $quantities[$index];
                    $item['first_image'] = explode(',', $item['Image'])[0]; // add first img
                    $items[] = $item;
                    $item_stmt->close();
                }

                $order['items'] = $items;
                $response['status'] = 'success';
                $response['order'] = $order;
            } else {
                $response['message'] = 'Order not found.';
            }

            $stmt->close();
        } else {
            $response['message'] = 'Order ID is required.';
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