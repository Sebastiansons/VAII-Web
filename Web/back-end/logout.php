<?php

function Logout() {
    $pastDate = "Thu, 01 Jan 1970 00:00:00 UTC";
    setcookie("sessionID", "", strtotime($pastDate), "/");
    setcookie("username", "", strtotime($pastDate), "/");
    setcookie("role", "", strtotime($pastDate), "/");
    setcookie("balance", "", strtotime($pastDate), "/");

    session_start();
    session_unset();
    session_destroy();

    session_start();
    session_regenerate_id(true);

    $response = array('status' => 'success', 'message' => 'Logged out successfully.');
    echo json_encode($response);
}

Logout();
?>