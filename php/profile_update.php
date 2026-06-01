<?php
require __DIR__ . '/session_check.php';
require __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$phone = $_POST['phone'];

$stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $phone, $user_id);
$stmt->execute();

header('Location: ../profile.php');
exit;
?>