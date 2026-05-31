<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    // Check if request is AJAX
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
       isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Not logged in', 'redirect' => 'login.html']);
        exit;
    }
    header('Location: login.html');
    exit;
}
?>