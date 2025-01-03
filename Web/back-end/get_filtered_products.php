<?php
    include 'index.php';

    header('Content-Type: application/json');

    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
    $search_name = isset($_GET['search_name']) ? mysqli_real_escape_string($conn, $_GET['search_name']) : '';
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = 6; // Konštantný limit 6 položiek
    $offset = ($page - 1) * $limit;

    // Získanie celkového poètu položiek
    $count_sql = "SELECT COUNT(*) as total FROM ShopItems WHERE 1=1";

    if ($category_id > 0) {
        $count_sql .= " AND CategoryID = $category_id";
    }

    if (!empty($search_name)) {
        $count_sql .= " AND Name LIKE '%$search_name%'";
    }

    if ($max_price > 0) {
        $count_sql .= " AND Price <= $max_price";
    }

    $count_result = mysqli_query($conn, $count_sql);
    $total_items = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_items / $limit);

    // Získanie položiek pre aktuálnu stránku
    $sql = "SELECT ItemID, Name, Description, Price, Image FROM ShopItems WHERE 1=1";

    if ($category_id > 0) {
        $sql .= " AND CategoryID = $category_id";
    }

    if (!empty($search_name)) {
        $sql .= " AND Name LIKE '%$search_name%'";
    }

    if ($max_price > 0) {
        $sql .= " AND Price <= $max_price";
    }

    $sql .= " LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $sql);

    $items = array();

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }

    echo json_encode([
        'items' => $items,
        'total_pages' => $total_pages,
        'current_page' => $page
    ]);

    mysqli_close($conn);
?>