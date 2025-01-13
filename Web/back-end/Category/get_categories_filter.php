<?php
include '../index.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '', 'data' => array());

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT CategoryID, Name FROM ShopCategories WHERE IsUnavailable = ?";
    $stmt = $conn->prepare($sql);
    $isUnavailable = 0;
    $stmt->bind_param("i", $isUnavailable);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }
            $response['status'] = 'success';
        } else {
            $response['message'] = 'No categories found.';
        }
    } else {
        $response['message'] = 'Failed to execute query: ' . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>