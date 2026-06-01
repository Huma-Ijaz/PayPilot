<?php
require __DIR__ . '/session_check.php';
require __DIR__ . '/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$recipient = $data['recipient'];
$amount    = $data['amount'];
$note      = $data['note'];
$user_id = $_SESSION['user_id'];

if (empty($recipient)) {
    echo json_encode(['success' => false, 'error' => 'Recipient is required']);
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid amount']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, recipient, note, status) VALUES (?, 'sent', ?, ?, ?, 'completed')");
$stmt->bind_param("idss", $user_id, $amount, $recipient, $note);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>