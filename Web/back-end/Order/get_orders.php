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
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [];
        if (isset($_GET['orderID']) && $_GET['orderID'] !== '') {
            $filters[] = "orderID = '" . $conn->real_escape_string($_GET['orderID']) . "'";
        }
        if (isset($_GET['dateFrom']) && $_GET['dateFrom'] !== '') {
            $filters[] = "created_at >= '" . $conn->real_escape_string($_GET['dateFrom']) . "'";
        }
        if (isset($_GET['dateTo']) && $_GET['dateTo'] !== '') {
            $filters[] = "created_at <= '" . $conn->real_escape_string($_GET['dateTo']) . "'";
        }
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $filters[] = "statusID = '" . $conn->real_escape_string($_GET['status']) . "'";
        }

        $filterSql = '';
        if (count($filters) > 0) {
            $filterSql = ' AND ' . implode(' AND ', $filters);
        }

        if ($role == 'Customer' && $clientID) {
            $sql = "SELECT orderID, created_at, statusID, (SELECT SUM(si.Price * FIND_IN_SET(si.ItemID, o.itemIDs)) FROM shopitems si WHERE FIND_IN_SET(si.ItemID, o.itemIDs)) AS total_price FROM orders o WHERE clientID = ? $filterSql LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $clientID, $limit, $offset);
        } elseif ($role == 'Support') {
            $sql = "SELECT orderID, created_at, statusID, (SELECT SUM(si.Price * FIND_IN_SET(si.ItemID, o.itemIDs)) FROM shopitems si WHERE FIND_IN_SET(si.ItemID, o.itemIDs)) AS total_price FROM orders o WHERE 1=1 $filterSql LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
        } else {
            $response['message'] = 'You do not have permission.';
            echo json_encode($response);
            exit;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);

        $count_sql = ($role == 'Customer') ? "SELECT COUNT(*) AS total FROM orders WHERE clientID = ? $filterSql" : "SELECT COUNT(*) AS total FROM orders WHERE 1=1 $filterSql";
        $count_stmt = $conn->prepare($count_sql);
        if ($role == 'Customer') {
            $count_stmt->bind_param("i", $clientID);
        }
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $total_orders = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_orders / $limit);

        // Prevod statusID na statusName
        $status_sql = "SELECT statusID, statusName FROM order_status";
        $status_result = $conn->query($status_sql);
        $status_map = [];
        while ($row = $status_result->fetch_assoc()) {
            $status_map[$row['statusID']] = $row['statusName'];
        }

        foreach ($orders as &$order) {
            $order['statusName'] = $status_map[$order['statusID']];
        }

        $response['status'] = 'success';
        $response['orders'] = $orders;
        $response['total_pages'] = $total_pages;

        $stmt->close();
        $count_stmt->close();
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