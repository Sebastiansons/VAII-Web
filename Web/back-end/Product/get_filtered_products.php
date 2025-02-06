<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '', 'data' => array());

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$count_sql = "SELECT COUNT(*) as total FROM ShopItems WHERE 1=1";
$params = [];
$types = '';

$sessionID = $_COOKIE['sessionID'] ?? null;

if ($sessionID) {
    $response = CheckSession($conn);
}

if ($category_id > 0) {
    $count_sql .= " AND CategoryID = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if (!empty($search_name)) {
    $count_sql .= " AND Name LIKE ?";
    $params[] = '%' . $search_name . '%';
    $types .= 's';
}

if ($min_price > 0) {
    $count_sql .= " AND Price >= ?";
    $params[] = $min_price;
    $types .= 'd';
}

if ($max_price > 0) {
    $count_sql .= " AND Price <= ?";
    $params[] = $max_price;
    $types .= 'd';
}

$stmt = $conn->prepare($count_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$count_result = $stmt->get_result();
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $limit);
$stmt->close();

$sql = "SELECT ItemID, Name, Description, Price, Image FROM ShopItems WHERE 1=1";
$params = [];
$types = '';

if ($category_id > 0) {
    $sql .= " AND CategoryID = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if (!empty($search_name)) {
    $sql .= " AND Name LIKE ?";
    $params[] = '%' . $search_name . '%';
    $types .= 's';
}

if ($min_price > 0) {
    $sql .= " AND Price >= ?";
    $params[] = $min_price;
    $types .= 'd';
}

if ($max_price > 0) {
    $sql .= " AND Price <= ?";
    $params[] = $max_price;
    $types .= 'd';
}

$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$items = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

$response['status'] = 'success';
$response['data'] = [
    'items' => $items,
    'total_pages' => $total_pages,
    'current_page' => $page
];

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}

$stmt->close();
$conn->close();
?>