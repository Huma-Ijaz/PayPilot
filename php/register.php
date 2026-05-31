<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $cnic     = $_POST['cnic'];
    $password = $_POST['password'];
    
    // Hash the password for security
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, cnic, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hash, $cnic, $phone);
    
    if ($stmt->execute()) {
        // Success - start session and go to dashboard
        session_start();
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['name']    = $name;
        header('Location: ../dashboard.php');
        exit;
    } else {
        // Email already exists
        header('Location: register.html?error=Email already exists');
        exit;
    }
}
?>
