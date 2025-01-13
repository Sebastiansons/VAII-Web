<?php
    include 'index.php';
    require 'verify_sessionID.php';

    header('Content-Type: application/json');

    $response = array('status' => 'error', 'message' => '');

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $sessionID = $_COOKIE['sessionID'] ?? null;

        if ($sessionID) {
            $response = CheckSession($conn);
        }

        $sql = "SELECT CategoryID, Name, Description, Icon, IsNew FROM ShopCategories WHERE IsUnavailable = '0'";
        $sql_stmt = $conn->prepare($sql);
        $sql_stmt->execute();
        $result = $sql_stmt->get_result();

        if ($result->num_rows > 0) {
            $categories = array();
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            $response['status'] = 'success'; 
            $response['categories'] = $categories;
        } else {
            $response['message'] = "No categories found.";
        }

        $sql_stmt->close();
        $conn->close();
    }

    echo json_encode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_last_error_msg();
    }
?>
