<?php

function Logout() {
    // Nastavenie minulého dátumu pre vymazanie cookie
    $pastDate = "Thu, 01 Jan 1970 00:00:00 UTC";
    setcookie("sessionID", "", strtotime($pastDate), "/");
    setcookie("username", "", strtotime($pastDate), "/");
    setcookie("role", "", strtotime($pastDate), "/");
    setcookie("balance", "", strtotime($pastDate), "/");

    // Ukonèenie session na serveri
    session_start();
    session_unset();
    session_destroy();

    // Odpoveï pre klienta
    $response = array('status' => 'success', 'message' => 'Logged out successfully.');
    echo json_encode($response);
}

Logout();
?>