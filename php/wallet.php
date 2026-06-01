<?php
require __DIR__ . 'session_check.php';
require __DIR__ . '/db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

// If GET request — return recent top-ups
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'topup' ORDER BY created_at DESC LIMIT 5");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    echo json_encode($rows);
    exit;
}

// If POST request — insert top-up
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data   = json_decode(file_get_contents('php://input'), true);
    $amount = $data['amount'];
    $method = $data['method'];

    if (!is_numeric($amount) || $amount <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid amount']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, recipient, note, status) VALUES (?, 'topup', ?, ?, '', 'completed')");
    $stmt->bind_param("ids", $user_id, $amount, $method);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
    exit;
}
?>